<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemoryRequest;
use App\Http\Resources\MemoryResource;
use App\Models\Memory;
use App\Repositories\MemoryRepository;
use App\Jobs\ProcessMemoryImage;
use App\Events\MemoryCreated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MemoryController extends Controller
{
    protected MemoryRepository $memoryRepository;

    public function __construct(MemoryRepository $memoryRepository)
    {
        $this->memoryRepository = $memoryRepository;
    }

    /**
     * Display a listing of memories
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'tags', 'date_from', 'date_to', 'shared']);
        $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
        
        $memories = $this->memoryRepository->getUserMemories(
            $request->user()->id,
            $filters,
            $perPage
        );

        return MemoryResource::collection($memories);
    }

    /**
     * Store a newly created memory
     */
    public function store(StoreMemoryRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->getProcessedData();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->handleImageUpload($request->file('image'));
            }

            // Create memory
            $memory = $this->memoryRepository->create($data);

            // Process image in background if uploaded
            if (isset($data['image_path'])) {
                ProcessMemoryImage::dispatch($memory);
            }

            // Get shared users for event
            $sharedWith = $request->input('shared_with', []);
            
            // Trigger memory created event
            event(new MemoryCreated($memory, $request->user(), $sharedWith));

            DB::commit();

            Log::info('Memory created successfully', [
                'memory_id' => $memory->id,
                'user_id' => $request->user()->id,
                'has_image' => isset($data['image_path']),
                'shared_with_count' => count($sharedWith)
            ]);

            return response()->json([
                'message' => 'Memory created successfully',
                'data' => new MemoryResource($memory)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded image if exists
            if (isset($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }

            Log::error('Failed to create memory', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to create memory',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified memory
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $memory = $this->memoryRepository->findById($id);

        if (!$memory) {
            return response()->json([
                'message' => 'Memory not found'
            ], 404);
        }

        // Check if user can view this memory
        if (!$this->canViewMemory($request->user(), $memory)) {
            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'data' => new MemoryResource($memory)
        ]);
    }

    /**
     * Update the specified memory
     */
    public function update(StoreMemoryRequest $request, int $id): JsonResponse
    {
        try {
            $memory = $this->memoryRepository->findById($id);

            if (!$memory) {
                return response()->json([
                    'message' => 'Memory not found'
                ], 404);
            }

            // Check ownership
            if ($memory->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }

            DB::beginTransaction();

            $data = $request->getProcessedData();
            $oldImagePath = $memory->image_path;

            // Handle new image upload
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->handleImageUpload($request->file('image'));
                $data['image_processed'] = false; // Mark for reprocessing
            }

            // Update memory
            $memory = $this->memoryRepository->update($memory, $data);

            // Process new image in background
            if ($request->hasFile('image')) {
                ProcessMemoryImage::dispatch($memory);
                
                // Delete old image
                if ($oldImagePath) {
                    $this->deleteImageFiles($oldImagePath);
                }
            }

            DB::commit();

            Log::info('Memory updated successfully', [
                'memory_id' => $memory->id,
                'user_id' => $request->user()->id,
                'image_updated' => $request->hasFile('image')
            ]);

            return response()->json([
                'message' => 'Memory updated successfully',
                'data' => new MemoryResource($memory)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up new image if upload failed
            if (isset($data['image_path']) && $data['image_path'] !== $oldImagePath) {
                Storage::disk('public')->delete($data['image_path']);
            }

            Log::error('Failed to update memory', [
                'memory_id' => $id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to update memory',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified memory
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $memory = $this->memoryRepository->findById($id);

            if (!$memory) {
                return response()->json([
                    'message' => 'Memory not found'
                ], 404);
            }

            // Check ownership
            if ($memory->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }

            $imagePath = $memory->image_path;

            // Delete memory
            $this->memoryRepository->delete($memory);

            // Delete associated image files
            if ($imagePath) {
                $this->deleteImageFiles($imagePath);
            }

            Log::info('Memory deleted successfully', [
                'memory_id' => $id,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'message' => 'Memory deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete memory', [
                'memory_id' => $id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to delete memory',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get memories for calendar view
     */
    public function calendar(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $memories = $this->memoryRepository->getMemoriesForCalendar(
            $request->user()->id,
            $year,
            $month
        );

        return response()->json([
            'data' => $memories
        ]);
    }

    /**
     * Search memories
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'per_page' => 'integer|min:1|max:50'
        ]);

        $query = $request->get('q');
        $perPage = $request->get('per_page', 15);

        $memories = $this->memoryRepository->searchMemories(
            $request->user()->id,
            $query,
            $perPage
        );

        return MemoryResource::collection($memories);
    }

    /**
     * Get memory statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->memoryRepository->getUserStatistics($request->user()->id);

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'memories/' . now()->format('Y/m');
        
        return $file->storeAs($path, $filename, 'public');
    }

    /**
     * Delete image files (original and thumbnails)
     */
    private function deleteImageFiles(string $imagePath): void
    {
        try {
            // Delete original image
            Storage::disk('public')->delete($imagePath);

            // Delete thumbnails
            $pathInfo = pathinfo($imagePath);
            $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'];

            $thumbnailSizes = ['thumbnail', 'medium', 'large'];
            foreach ($thumbnailSizes as $size) {
                $thumbnailPath = $thumbnailDir . '/' . $filename . '_' . $size . '.' . $extension;
                Storage::disk('public')->delete($thumbnailPath);
            }

        } catch (\Exception $e) {
            Log::warning('Failed to delete some image files', [
                'image_path' => $imagePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user can view memory
     */
    private function canViewMemory($user, Memory $memory): bool
    {
        // Owner can always view
        if ($memory->user_id === $user->id) {
            return true;
        }

        // Public memories can be viewed by anyone
        if (!$memory->is_private) {
            return true;
        }

        // Private memories can only be viewed by owner
        // (In future, you might add sharing functionality)
        return false;
    }
}