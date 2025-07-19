<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Import our custom events and listeners
use App\Events\MemoryCreated;
use App\Events\BirthdaySurpriseTriggered;
use App\Listeners\SendMemoryNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Memory Events
        MemoryCreated::class => [
            SendMemoryNotification::class,
        ],

        // Birthday Surprise Events
        BirthdaySurpriseTriggered::class => [
            // Add listeners here if needed
        ],

        // Laravel's built-in events
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogUserLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogUserLogout',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\LogFailedLogin',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register model events
        $this->registerModelEvents();
        
        // Register custom event listeners
        $this->registerCustomEvents();
    }

    /**
     * Register model events
     */
    protected function registerModelEvents(): void
    {
        // Memory model events
        \App\Models\Memory::creating(function ($memory) {
            // Set UUID if not provided
            if (!$memory->uuid) {
                $memory->uuid = \Illuminate\Support\Str::uuid();
            }
        });

        \App\Models\Memory::created(function ($memory) {
            // Clear user's memory cache
            \Illuminate\Support\Facades\Cache::forget("user_memories_{$memory->user_id}");
            \Illuminate\Support\Facades\Cache::forget("user_memory_stats_{$memory->user_id}");
        });

        \App\Models\Memory::updated(function ($memory) {
            // Clear user's memory cache
            \Illuminate\Support\Facades\Cache::forget("user_memories_{$memory->user_id}");
            \Illuminate\Support\Facades\Cache::forget("user_memory_stats_{$memory->user_id}");
        });

        \App\Models\Memory::deleted(function ($memory) {
            // Clear user's memory cache
            \Illuminate\Support\Facades\Cache::forget("user_memories_{$memory->user_id}");
            \Illuminate\Support\Facades\Cache::forget("user_memory_stats_{$memory->user_id}");
        });

        // User model events
        \App\Models\User::created(function ($user) {
            // Schedule birthday surprise check if birthday is set
            if ($user->birthday) {
                $this->scheduleBirthdayCheck($user);
            }
        });

        \App\Models\User::updated(function ($user) {
            // Reschedule birthday surprise if birthday changed
            if ($user->isDirty('birthday') && $user->birthday) {
                $this->scheduleBirthdayCheck($user);
            }
        });

        // Event model events
        if (class_exists('\App\Models\Event')) {
            \App\Models\Event::creating(function ($event) {
                if (!$event->uuid) {
                    $event->uuid = \Illuminate\Support\Str::uuid();
                }
            });
        }

        // Birthday Surprise model events
        if (class_exists('\App\Models\BirthdaySurprise')) {
            \App\Models\BirthdaySurprise::creating(function ($surprise) {
                if (!$surprise->uuid) {
                    $surprise->uuid = \Illuminate\Support\Str::uuid();
                }
            });
        }
    }

    /**
     * Register custom event listeners
     */
    protected function registerCustomEvents(): void
    {
        // Listen for queue job failures
        Event::listen('queue.failed', function ($event) {
            \Illuminate\Support\Facades\Log::error('Queue job failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'payload' => $event->job->payload(),
                'exception' => $event->exception->getMessage(),
            ]);
        });

        // Listen for broadcasting events
        Event::listen('broadcasting.*', function ($eventName, $data) {
            \Illuminate\Support\Facades\Log::debug('Broadcasting event', [
                'event' => $eventName,
                'data' => $data
            ]);
        });

        // Listen for cache events in development
        if (app()->environment('local')) {
            Event::listen('cache:*', function ($eventName, $data) {
                \Illuminate\Support\Facades\Log::debug('Cache event', [
                    'event' => $eventName,
                    'data' => $data
                ]);
            });
        }
    }

    /**
     * Schedule birthday surprise check for user
     */
    protected function scheduleBirthdayCheck(\App\Models\User $user): void
    {
        try {
            // Calculate next birthday
            $birthday = $user->birthday;
            $nextBirthday = $birthday->copy()->year(now()->year);
            
            // If birthday has passed this year, schedule for next year
            if ($nextBirthday->isPast()) {
                $nextBirthday->addYear();
            }

            // Schedule the job to run on the birthday
            \App\Jobs\TriggerBirthdaySurprise::dispatch($user)
                ->delay($nextBirthday->startOfDay());

            \Illuminate\Support\Facades\Log::info('Birthday surprise scheduled', [
                'user_id' => $user->id,
                'birthday' => $birthday->format('Y-m-d'),
                'next_birthday' => $nextBirthday->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to schedule birthday surprise', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}