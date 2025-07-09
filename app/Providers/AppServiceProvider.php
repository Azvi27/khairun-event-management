<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ TAMBAHKAN: Blade directive untuk storage URL
        Blade::directive('storageUrl', function ($path) {
            return "<?php echo app('App\Services\StorageService')->getFileUrl($path); ?>";
        });
    }
}
