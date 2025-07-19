<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Memory;
use App\Models\User;

class OptimizeApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'khairun:optimize 
                            {--clear-cache : Clear all caches}
                            {--optimize-images : Optimize and generate thumbnails}
                            {--cleanup-temp : Clean up temporary files}
                            {--database-maintenance : Run database maintenance}
                            {--all : Run all optimizations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize Khairun application performance and clean up resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Khairun Application Optimization...');
        $this->newLine();

        $startTime = microtime(true);

        if ($this->option('all')) {
            $this->runAllOptimizations();
        } else {
            $this->runSelectedOptimizations();
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->newLine();
        $this->info("✅ Optimization completed in {$duration} seconds!");
    }

    /**
     * Run all optimizations
     */
    private function runAllOptimizations()
    {
        $this->clearCaches();
        $this->optimizeImages();
        $this->cleanupTempFiles();
        $this->runDatabaseMaintenance();
        $this->optimizeApplication();
    }

    /**
     * Run selected optimizations based on options
     */
    private function runSelectedOptimizations()
    {
        if ($this->option('clear-cache')) {
            $this->clearCaches();
        }

        if ($this->option('optimize-images')) {
            $this->optimizeImages();
        }

        if ($this->option('cleanup-temp')) {
            $this->cleanupTempFiles();
        }

        if ($this->option('database-maintenance')) {
            $this->runDatabaseMaintenance();
        }

        // If no specific options, run basic optimization
        if (!$this->hasAnyOption()) {
            $this->optimizeApplication();
        }
    }

    /**
     * Clear all caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        // Clear application cache
        Cache::flush();
        $this->line('   ✓ Application cache cleared');
        
        // Clear Laravel caches
        Artisan::call('config:clear');
        $this->line('   ✓ Configuration cache cleared');
        
        Artisan::call('route:clear');
        $this->line('   ✓ Route cache cleared');
        
        Artisan::call('view:clear');
        $this->line('   ✓ View cache cleared');
        
        // Clear compiled views
        Artisan::call('clear-compiled');
        $this->line('   ✓ Compiled files cleared');
    }

    /**
     * Optimize images and generate thumbnails
     */
    private function optimizeImages()
    {
        $this->info('🖼️  Optimizing images...');
        
        $memories = Memory::whereNotNull('image_path')->get();
        $processed = 0;
        $errors = 0;

        foreach ($memories as $memory) {
            try {
                $this->generateThumbnail($memory);
                $processed++;
            } catch (\Exception $e) {
                $errors++;
                $this->warn("   ⚠️  Failed to process image for memory ID {$memory->id}: {$e->getMessage()}");
            }
        }

        $this->line("   ✓ Processed {$processed} images");
        if ($errors > 0) {
            $this->warn("   ⚠️  {$errors} images failed to process");
        }
    }

    /**
     * Generate thumbnail for memory image
     */
    private function generateThumbnail(Memory $memory)
    {
        if (!$memory->image_path || !Storage::disk('public')->exists($memory->image_path)) {
            return;
        }

        $pathInfo = pathinfo($memory->image_path);
        $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
        $thumbnailPath = $thumbnailDir . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        // Skip if thumbnail already exists
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return;
        }

        // Create thumbnail directory if it doesn't exist
        if (!Storage::disk('public')->exists($thumbnailDir)) {
            Storage::disk('public')->makeDirectory($thumbnailDir);
        }

        // Here you would implement actual image resizing
        // For now, we'll just copy the original (placeholder)
        $originalPath = Storage::disk('public')->path($memory->image_path);
        $thumbPath = Storage::disk('public')->path($thumbnailPath);
        
        // This is a placeholder - implement actual image resizing with intervention/image or similar
        copy($originalPath, $thumbPath);
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles()
    {
        $this->info('🗑️  Cleaning up temporary files...');
        
        $tempDirs = [
            storage_path('app/temp'),
            storage_path('framework/cache'),
            storage_path('logs')
        ];

        $deletedFiles = 0;
        $deletedSize = 0;

        foreach ($tempDirs as $dir) {
            if (is_dir($dir)) {
                $result = $this->cleanDirectory($dir);
                $deletedFiles += $result['files'];
                $deletedSize += $result['size'];
            }
        }

        $sizeFormatted = $this->formatBytes($deletedSize);
        $this->line("   ✓ Deleted {$deletedFiles} temporary files ({$sizeFormatted})");
    }

    /**
     * Run database maintenance
     */
    private function runDatabaseMaintenance()
    {
        $this->info('🗄️  Running database maintenance...');
        
        // Clean up old sessions
        DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(30)->timestamp)
            ->delete();
        $this->line('   ✓ Cleaned old sessions');
        
        // Clean up old cache entries
        DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->delete();
        $this->line('   ✓ Cleaned expired cache entries');
        
        // Optimize tables (MySQL only)
        if (config('database.default') === 'mysql') {
            $tables = ['users', 'memories', 'events', 'birthday_surprises'];
            foreach ($tables as $table) {
                try {
                    DB::statement("OPTIMIZE TABLE {$table}");
                } catch (\Exception $e) {
                    // Ignore errors for non-existent tables
                }
            }
            $this->line('   ✓ Optimized database tables');
        }
    }

    /**
     * Run Laravel optimizations
     */
    private function optimizeApplication()
    {
        $this->info('⚡ Running Laravel optimizations...');
        
        // Cache configurations
        Artisan::call('config:cache');
        $this->line('   ✓ Configuration cached');
        
        // Cache routes
        Artisan::call('route:cache');
        $this->line('   ✓ Routes cached');
        
        // Cache views
        Artisan::call('view:cache');
        $this->line('   ✓ Views cached');
        
        // Optimize autoloader
        if (app()->environment('production')) {
            exec('composer dump-autoload --optimize --no-dev');
            $this->line('   ✓ Autoloader optimized');
        }
    }

    /**
     * Clean directory and return stats
     */
    private function cleanDirectory(string $dir): array
    {
        $files = 0;
        $size = 0;
        
        if (!is_dir($dir)) {
            return ['files' => 0, 'size' => 0];
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < (time() - 86400)) { // 24 hours old
                $size += $file->getSize();
                unlink($file->getRealPath());
                $files++;
            }
        }
        
        return ['files' => $files, 'size' => $size];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Check if any optimization option is set
     */
    private function hasAnyOption(): bool
    {
        return $this->option('clear-cache') ||
               $this->option('optimize-images') ||
               $this->option('cleanup-temp') ||
               $this->option('database-maintenance');
    }
}