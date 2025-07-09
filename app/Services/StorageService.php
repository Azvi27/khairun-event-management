<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class StorageService
{
    protected $disk;
    protected $isLocal;

    public function __construct()
    {
        $this->disk = env('FILESYSTEM_DISK', 'public');
        $this->isLocal = app()->environment('local') || $this->disk === 'public';
    }

    /**
     * Upload file dengan auto-detection environment
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', ?string $filename = null): ?string
    {
        try {
            // Generate filename jika tidak diberikan
            if (!$filename) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            }

            $path = $folder . '/' . $filename;

            if ($this->isLocal) {
                // Local storage untuk development
                $fullPath = $file->storeAs($folder, $filename, 'public');
                Log::info('File uploaded locally', ['path' => $fullPath]);
                return $fullPath;
            } else {
                // CloudFlare R2 untuk production
                $fullPath = $file->storeAs($folder, $filename, 'r2');
                Log::info('File uploaded to R2', ['path' => $fullPath]);
                return $fullPath;
            }
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'folder' => $folder
            ]);
            return null;
        }
    }

    /**
     * Get URL file
     */
    public function getFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        try {
            if ($this->isLocal) {
                return Storage::disk('public')->url($path);
            } else {
                return Storage::disk('r2')->url($path);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get file URL', ['path' => $path, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Delete file
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            if ($this->isLocal) {
                return Storage::disk('public')->delete($path);
            } else {
                return Storage::disk('r2')->delete($path);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete file', ['path' => $path, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if file exists
     */
    public function fileExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            if ($this->isLocal) {
                return Storage::disk('public')->exists($path);
            } else {
                return Storage::disk('r2')->exists($path);
            }
        } catch (\Exception $e) {
            Log::error('Failed to check file existence', ['path' => $path, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Upload image dengan validation dan optimization
     */
    public function uploadImage(UploadedFile $file, string $folder = 'images', ?string $filename = null): ?string
    {
        // Validate image
        if (!in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            Log::warning('Invalid image format', ['file' => $file->getClientOriginalName()]);
            return null;
        }

        // Check file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            Log::warning('Image file too large', ['size' => $file->getSize()]);
            return null;
        }

        return $this->uploadFile($file, $folder, $filename);
    }

    /**
     * Get disk info
     */
    public function getDiskInfo(): array
    {
        return [
            'disk' => $this->disk,
            'is_local' => $this->isLocal,
            'environment' => app()->environment()
        ];
    }
}