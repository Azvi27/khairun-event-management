<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'memory_date' => $this->memory_date?->format('Y-m-d'),
            'memory_date_formatted' => $this->memory_date?->format('d F Y'),
            'memory_date_human' => $this->memory_date?->diffForHumans(),
            'location' => $this->location,
            'is_private' => $this->is_private,
            'tags' => $this->tags ?? [],
            
            // Image handling
            'image_url' => $this->getImageUrl(),
            'image_thumbnail' => $this->getImageThumbnail(),
            'has_image' => !empty($this->image_path),
            
            // User information (when available)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'avatar' => $this->user->avatar ?? null,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
            
            // Additional metadata
            'meta' => [
                'can_edit' => $this->canEdit($request->user()),
                'can_delete' => $this->canDelete($request->user()),
                'is_owner' => $this->isOwner($request->user()),
                'days_ago' => $this->memory_date?->diffInDays(now()),
                'year' => $this->memory_date?->year,
                'month' => $this->memory_date?->month,
                'day' => $this->memory_date?->day,
            ],
            
            // Include sensitive data only for owner
            $this->mergeWhen($this->isOwner($request->user()), [
                'image_path' => $this->image_path,
                'original_filename' => $this->original_filename,
            ]),
        ];
    }

    /**
     * Get the image URL
     */
    private function getImageUrl(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        // Check if it's a full URL (external image)
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        // Local storage
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return null;
    }

    /**
     * Get thumbnail URL (if available)
     */
    private function getImageThumbnail(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        // Generate thumbnail path
        $pathInfo = pathinfo($this->image_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }

        // Fallback to original image
        return $this->getImageUrl();
    }

    /**
     * Check if user can edit this memory
     */
    private function canEdit($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Check if user can delete this memory
     */
    private function canDelete($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Check if user is the owner of this memory
     */
    private function isOwner($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Get additional data when transforming for API
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }

    /**
     * Customize the response for specific contexts
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'total' => $resource instanceof \Illuminate\Pagination\LengthAwarePaginator 
                    ? $resource->total() 
                    : $resource->count(),
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }
}