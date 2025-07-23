<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeProduction extends Command
{
    protected $signature = 'khairun:optimize {--force}';
    protected $description = 'Optimize Khairun application for production';

    public function handle()
    {
        $this->info('🚀 Optimizing Khairun for Production...');
        
        if (!app()->environment('production') && !$this->option('force')) {
            $this->error('❌ This command should only be run in production environment.');
            $this->line('Use --force flag to override this check.');
            return 1;
        }

        $steps = [
            'Clear all caches' => 'cache:clear',
            'Clear config cache' => 'config:clear',
            'Clear route cache' => 'route:clear',
            'Clear view cache' => 'view:clear',
            'Clear compiled cache' => 'clear-compiled',
            'Cache configurations' => 'config:cache',
            'Cache routes' => 'route:cache',
            'Cache views' => 'view:cache',
            'Cache events' => 'event:cache',
            'Optimize autoloader' => null, // Will be handled separately
            'Create storage link' => 'storage:link',
        ];

        foreach ($steps as $description => $command) {
            $this->line("⏳ {$description}...");
            
            if ($command === null) {
                // Handle composer optimization
                exec('composer install --optimize-autoloader --no-dev --quiet 2>&1', $output, $returnCode);
                if ($returnCode === 0) {
                    $this->line("   ✅ Completed");
                } else {
                    $this->line("   ⚠️  Warning: " . implode("\n", $output));
                }
            } else {
                try {
                    Artisan::call($command);
                    $this->line("   ✅ Completed");
                } catch (\Exception $e) {
                    $this->line("   ⚠️  Warning: " . $e->getMessage());
                }
            }
        }

        // Set proper file permissions
        $this->line("⏳ Setting file permissions...");
        try {
            $storagePath = storage_path();
            $bootstrapPath = base_path('bootstrap/cache');
            
            exec("chmod -R 775 {$storagePath} 2>&1", $output, $returnCode);
            exec("chmod -R 775 {$bootstrapPath} 2>&1", $output, $returnCode);
            
            $this->line("   ✅ File permissions set");
        } catch (\Exception $e) {
            $this->line("   ⚠️  Warning: Could not set file permissions");
        }

        $this->info('🎉 Production optimization completed!');
        $this->line('');
        $this->line('📋 Next steps:');
        $this->line('   1. Run: php artisan migrate --force');
        $this->line('   2. Run: php artisan queue:restart');
        $this->line('   3. Run: php artisan system:health-check');
        
        return 0;
    }
} 