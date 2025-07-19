<?php

namespace App\Jobs;

use App\Models\Memory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessMemoryImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Memory $memory;
    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
        $this->onQueue('image-processing');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->memory->image_path || !Storage::disk('public')->exists($this->memory->image_path)) {
                Log::warning('Memory image not found for processing', [
                    'memory_id' => $this->memory->id,
                    'image_path' => $this->memory->image_path
                ]);
                return;
            }

            $originalPath = Storage::disk('public')->path($this->memory->image_path);
            $pathInfo = pathinfo($this->memory->image_path);
            
            // Create thumbnails directory
            $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir);
            }

            // Generate different sizes
            $sizes = [
                'thumbnail' => ['width' => 300, 'height' => 300],
                'medium' => ['width' => 800, 'height' => 600],
                'large' => ['width' => 1200, 'height' => 900]
            ];

            foreach ($sizes as $sizeName => $dimensions) {
                $this->generateResizedImage(
                    $originalPath,
                    $pathInfo,
                    $sizeName,
                    $dimensions['width'],
                    $dimensions['height']
                );
            }

            // Optimize original image
            $this->optimizeOriginalImage($originalPath);

            // Update memory with processing status
            $this->memory->update([
                'image_processed' => true,
                'processed_at' => now()
            ]);

            Log::info('Memory image processed successfully', [
                'memory_id' => $this->memory->id,
                'image_path' => $this->memory->image_path,
                'sizes_generated' => array_keys($sizes)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process memory image', [
                'memory_id' => $this->memory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Generate resized image
     */
    private function generateResizedImage(string $originalPath, array $pathInfo, string $sizeName, int $width, int $height): void
    {
        $resizedFileName = $pathInfo['filename'] . '_' . $sizeName . '.' . $pathInfo['extension'];
        $resizedPath = $pathInfo['dirname'] . '/thumbnails/' . $resizedFileName;
        $fullResizedPath = Storage::disk('public')->path($resizedPath);

        // Skip if already exists
        if (Storage::disk('public')->exists($resizedPath)) {
            return;
        }

        try {
            // Use Intervention Image if available, otherwise use basic PHP GD
            if (class_exists('Intervention\Image\Facades\Image')) {
                $image = Image::make($originalPath)
                    ->fit($width, $height, function ($constraint) {
                        $constraint->upsize();
                    })
                    ->sharpen(10)
                    ->save($fullResizedPath, 85); // 85% quality
            } else {
                $this->resizeImageWithGD($originalPath, $fullResizedPath, $width, $height);
            }

            Log::debug('Generated resized image', [
                'memory_id' => $this->memory->id,
                'size' => $sizeName,
                'dimensions' => "{$width}x{$height}",
                'path' => $resizedPath
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to generate resized image', [
                'memory_id' => $this->memory->id,
                'size' => $sizeName,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Resize image using GD library (fallback)
     */
    private function resizeImageWithGD(string $sourcePath, string $destPath, int $width, int $height): void
    {
        $imageInfo = getimagesize($sourcePath);
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate aspect ratio
        $aspectRatio = $sourceWidth / $sourceHeight;
        if ($width / $height > $aspectRatio) {
            $width = $height * $aspectRatio;
        } else {
            $height = $width / $aspectRatio;
        }

        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type: ' . $mimeType);
        }

        // Create destination image
        $destImage = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefill($destImage, 0, 0, $transparent);
        }

        // Resize
        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        // Save
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($destImage, $destPath, 85);
                break;
            case 'image/png':
                imagepng($destImage, $destPath, 8);
                break;
            case 'image/gif':
                imagegif($destImage, $destPath);
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destImage);
    }

    /**
     * Optimize original image
     */
    private function optimizeOriginalImage(string $imagePath): void
    {
        try {
            if (class_exists('Intervention\Image\Facades\Image')) {
                $image = Image::make($imagePath);
                
                // Only optimize if image is larger than 1920px width
                if ($image->width() > 1920) {
                    $image->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                $image->save($imagePath, 90); // 90% quality for original
            }
        } catch (\Exception $e) {
            Log::warning('Failed to optimize original image', [
                'memory_id' => $this->memory->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Memory image processing job failed permanently', [
            'memory_id' => $this->memory->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts
        ]);

        // Mark as failed in database
        $this->memory->update([
            'image_processed' => false,
            'processing_failed' => true,
            'processing_error' => $exception->getMessage()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'memory:' . $this->memory->id,
            'image-processing',
            'user:' . $this->memory->user_id
        ];
    }
}