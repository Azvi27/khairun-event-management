<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\TriggerBirthdaySurprise;
use App\Models\User;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily birthday surprise checks
        $schedule->call(function () {
            $this->checkBirthdaysToday();
        })
        ->daily()
        ->at('09:00')
        ->name('birthday-surprise-check')
        ->description('Check for birthdays today and trigger surprises');

        // Weekly application optimization
        $schedule->command('khairun:optimize --clear-cache --cleanup-temp')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->name('weekly-optimization')
            ->description('Weekly application optimization and cleanup');

        // Monthly full optimization including images
        $schedule->command('khairun:optimize --all')
            ->monthly()
            ->at('03:00')
            ->name('monthly-full-optimization')
            ->description('Monthly full application optimization');

        // Daily database maintenance
        $schedule->command('khairun:optimize --database-maintenance')
            ->daily()
            ->at('01:00')
            ->name('daily-db-maintenance')
            ->description('Daily database maintenance and cleanup');

        // Clear expired cache entries every 6 hours
        $schedule->command('cache:prune-stale-tags')
            ->everySixHours()
            ->name('cache-cleanup')
            ->description('Clean up expired cache entries');

        // Process failed jobs retry
        $schedule->command('queue:retry all')
            ->hourly()
            ->name('retry-failed-jobs')
            ->description('Retry failed queue jobs');

        // Clean up old notifications (older than 30 days)
        $schedule->call(function () {
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where('created_at', '<', now()->subDays(30))
                ->where('read_at', '!=', null)
                ->delete();
        })
        ->daily()
        ->at('04:00')
        ->name('cleanup-old-notifications')
        ->description('Clean up old read notifications');

        // Generate application statistics
        $schedule->call(function () {
            $this->generateDailyStatistics();
        })
        ->daily()
        ->at('23:55')
        ->name('generate-daily-stats')
        ->description('Generate daily application statistics');

        // Backup important data (if backup is configured)
        if (config('backup.enabled', false)) {
            $schedule->command('backup:run')
                ->daily()
                ->at('05:00')
                ->name('daily-backup')
                ->description('Daily application backup');
        }

        // Health check ping (if monitoring service is configured)
        if (config('monitoring.health_check_url')) {
            $schedule->call(function () {
                try {
                    $client = new \GuzzleHttp\Client();
                    $client->get(config('monitoring.health_check_url'), [
                        'timeout' => 10
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Health check ping failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            })
            ->everyFiveMinutes()
            ->name('health-check-ping')
            ->description('Send health check ping to monitoring service');
        }

        // Queue monitoring
        $schedule->call(function () {
            $this->monitorQueues();
        })
        ->everyTenMinutes()
        ->name('queue-monitoring')
        ->description('Monitor queue health and performance');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Check for birthdays today and trigger surprises
     */
    protected function checkBirthdaysToday(): void
    {
        try {
            $today = now();
            
            $users = User::whereNotNull('birthday')
                ->whereMonth('birthday', $today->month)
                ->whereDay('birthday', $today->day)
                ->get();

            foreach ($users as $user) {
                // Check if surprise already triggered today
                $cacheKey = "birthday_surprise_triggered_{$user->id}_" . $today->format('Y-m-d');
                
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    TriggerBirthdaySurprise::dispatch($user);
                    
                    \Illuminate\Support\Facades\Log::info('Birthday surprise job dispatched', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'birthday' => $user->birthday->format('Y-m-d')
                    ]);
                }
            }

            \Illuminate\Support\Facades\Log::info('Birthday check completed', [
                'date' => $today->format('Y-m-d'),
                'users_found' => $users->count()
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Birthday check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Generate daily application statistics
     */
    protected function generateDailyStatistics(): void
    {
        try {
            $stats = [
                'date' => now()->format('Y-m-d'),
                'total_users' => User::count(),
                'active_users_today' => User::whereDate('last_login_at', now())->count(),
                'total_memories' => \App\Models\Memory::count(),
                'memories_created_today' => \App\Models\Memory::whereDate('created_at', now())->count(),
                'total_events' => class_exists('\App\Models\Event') ? \App\Models\Event::count() : 0,
                'events_created_today' => class_exists('\App\Models\Event') ? \App\Models\Event::whereDate('created_at', now())->count() : 0,
                'birthday_surprises_triggered' => class_exists('\App\Models\BirthdaySurprise') ? \App\Models\BirthdaySurprise::whereDate('last_triggered_at', now())->count() : 0,
            ];

            // Store in cache for dashboard
            \Illuminate\Support\Facades\Cache::put('daily_stats_' . now()->format('Y-m-d'), $stats, now()->addDays(7));

            // Log for monitoring
            \Illuminate\Support\Facades\Log::info('Daily statistics generated', $stats);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to generate daily statistics', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Monitor queue health and performance
     */
    protected function monitorQueues(): void
    {
        try {
            $queues = ['default', 'image-processing', 'notifications', 'emails', 'broadcasts', 'birthday-surprises'];
            $queueStats = [];

            foreach ($queues as $queue) {
                $pendingJobs = \Illuminate\Support\Facades\DB::table('jobs')
                    ->where('queue', $queue)
                    ->count();

                $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')
                    ->where('queue', $queue)
                    ->count();

                $queueStats[$queue] = [
                    'pending' => $pendingJobs,
                    'failed' => $failedJobs
                ];

                // Alert if too many pending jobs
                if ($pendingJobs > 100) {
                    \Illuminate\Support\Facades\Log::warning('High number of pending jobs in queue', [
                        'queue' => $queue,
                        'pending_jobs' => $pendingJobs
                    ]);
                }

                // Alert if too many failed jobs
                if ($failedJobs > 10) {
                    \Illuminate\Support\Facades\Log::warning('High number of failed jobs in queue', [
                        'queue' => $queue,
                        'failed_jobs' => $failedJobs
                    ]);
                }
            }

            // Store queue stats in cache
            \Illuminate\Support\Facades\Cache::put('queue_stats', $queueStats, now()->addMinutes(15));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Queue monitoring failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}