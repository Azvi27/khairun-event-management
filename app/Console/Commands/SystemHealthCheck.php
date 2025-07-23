<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Models\User;
use App\Models\Memory;
use App\Services\SpotifyService;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check {--format=json}';
    protected $description = 'Check system health for production monitoring';

    public function handle()
    {
        $checks = [];
        $overallHealth = true;

        // Database Check
        try {
            DB::connection()->getPdo();
            User::count(); // Test actual query
            $checks['database'] = ['status' => 'healthy', 'message' => 'Connected and responsive'];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'failed', 'message' => $e->getMessage()];
            $overallHealth = false;
        }

        // Storage Check
        try {
            Storage::disk()->put('health-check.txt', 'test');
            Storage::disk()->delete('health-check.txt');
            $checks['storage'] = ['status' => 'healthy', 'message' => 'Read/write operations successful'];
        } catch (\Exception $e) {
            $checks['storage'] = ['status' => 'failed', 'message' => $e->getMessage()];
            $overallHealth = false;
        }

        // Cache Check
        try {
            Cache::put('health-check', 'test', 60);
            $value = Cache::get('health-check');
            Cache::forget('health-check');
            
            if ($value === 'test') {
                $checks['cache'] = ['status' => 'healthy', 'message' => 'Cache operations successful'];
            } else {
                throw new \Exception('Cache value mismatch');
            }
        } catch (\Exception $e) {
            $checks['cache'] = ['status' => 'failed', 'message' => $e->getMessage()];
            $overallHealth = false;
        }

        // Queue Check
        try {
            $queueConnection = config('queue.default');
            $checks['queue'] = ['status' => 'healthy', 'message' => "Queue connection ({$queueConnection}) is configured"];
        } catch (\Exception $e) {
            $checks['queue'] = ['status' => 'failed', 'message' => $e->getMessage()];
            $overallHealth = false;
        }

        // Spotify Service Check
        try {
            $spotify = new SpotifyService();
            if ($spotify->isMockMode()) {
                $checks['spotify'] = ['status' => 'warning', 'message' => 'Running in mock mode'];
            } else {
                $tracks = $spotify->searchTracks('test', 1);
                $checks['spotify'] = ['status' => 'healthy', 'message' => 'API connection successful'];
            }
        } catch (\Exception $e) {
            $checks['spotify'] = ['status' => 'failed', 'message' => $e->getMessage()];
            $overallHealth = false;
        }

        // Log health check results
        $this->info('System health check completed');
        $this->info('Overall health: ' . ($overallHealth ? 'healthy' : 'unhealthy'));
        
        // Send email notification if health is unhealthy
        if (!$overallHealth) {
            $this->error('System health check failed. Please check the logs for details.');
            $this->sendHealthCheckEmail();
        }
    }

    private function sendHealthCheckEmail()
    {
        $to = config('production.monitoring.health_check_email');
        $subject = 'System Health Check Alert';
        $message = 'System health check failed. Please check the logs for details.';
        
        Mail::to($to)->send(new HealthCheckNotification($subject, $message));
    }
}
