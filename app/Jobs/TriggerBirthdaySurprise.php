<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Memory;
use App\Models\BirthdaySurprise;
use App\Events\BirthdaySurpriseTriggered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TriggerBirthdaySurprise implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('birthday-surprises');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if user has a birthday today
            if (!$this->isBirthdayToday()) {
                Log::info('Not user birthday today, skipping surprise', [
                    'user_id' => $this->user->id,
                    'birthday' => $this->user->birthday?->format('Y-m-d')
                ]);
                return;
            }

            // Check if surprise already triggered today
            $cacheKey = "birthday_surprise_triggered_{$this->user->id}_" . now()->format('Y-m-d');
            if (Cache::has($cacheKey)) {
                Log::info('Birthday surprise already triggered today', [
                    'user_id' => $this->user->id
                ]);
                return;
            }

            // Get or create birthday surprise configuration
            $surprise = $this->getOrCreateBirthdaySurprise();
            
            if (!$surprise->is_active) {
                Log::info('Birthday surprise is disabled for user', [
                    'user_id' => $this->user->id
                ]);
                return;
            }

            // Collect memories for the surprise
            $memories = $this->collectMemoriesForSurprise();

            // Trigger the surprise event
            event(new BirthdaySurpriseTriggered($this->user, $surprise, $memories));

            // Mark surprise as triggered today
            Cache::put($cacheKey, true, now()->endOfDay());

            // Update surprise statistics
            $surprise->increment('times_triggered');
            $surprise->update(['last_triggered_at' => now()]);

            Log::info('Birthday surprise triggered successfully', [
                'user_id' => $this->user->id,
                'surprise_id' => $surprise->id,
                'memories_count' => count($memories)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to trigger birthday surprise', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Check if today is user's birthday
     */
    private function isBirthdayToday(): bool
    {
        if (!$this->user->birthday) {
            return false;
        }

        $today = now();
        $birthday = $this->user->birthday;

        return $today->month === $birthday->month && $today->day === $birthday->day;
    }

    /**
     * Get or create birthday surprise configuration
     */
    private function getOrCreateBirthdaySurprise(): BirthdaySurprise
    {
        $surprise = BirthdaySurprise::where('user_id', $this->user->id)->first();

        if (!$surprise) {
            $surprise = BirthdaySurprise::create([
                'user_id' => $this->user->id,
                'title' => '🎉 Happy Birthday, ' . $this->user->name . '!',
                'message' => $this->generateBirthdayMessage(),
                'surprise_type' => 'memory_slideshow',
                'background_music' => 'happy_birthday_classic',
                'animation_style' => 'confetti_celebration',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Created new birthday surprise configuration', [
                'user_id' => $this->user->id,
                'surprise_id' => $surprise->id
            ]);
        }

        return $surprise;
    }

    /**
     * Generate personalized birthday message
     */
    private function generateBirthdayMessage(): string
    {
        $age = $this->calculateAge();
        $memoryCount = Memory::where('user_id', $this->user->id)->count();
        
        $messages = [
            "🎂 Another year of wonderful memories! You've created {$memoryCount} beautiful moments so far.",
            "🌟 Celebrating {$age} years of amazing adventures and countless precious memories!",
            "🎈 Happy Birthday! Here's to another year of creating unforgettable moments.",
            "🎊 {$age} years young and still making memories that will last a lifetime!",
            "🎁 Your special day deserves a special celebration with all your favorite memories!"
        ];

        return $messages[array_rand($messages)];
    }

    /**
     * Calculate user's age
     */
    private function calculateAge(): int
    {
        if (!$this->user->birthday) {
            return 0;
        }

        return now()->diffInYears($this->user->birthday);
    }

    /**
     * Collect memories for the birthday surprise
     */
    private function collectMemoriesForSurprise(): array
    {
        // Get memories from the past year, prioritizing those with images
        $memories = Memory::where('user_id', $this->user->id)
            ->where('memory_date', '>=', now()->subYear())
            ->whereNotNull('image_path')
            ->orderBy('memory_date', 'desc')
            ->limit(10)
            ->get();

        // If not enough recent memories, get older ones
        if ($memories->count() < 5) {
            $additionalMemories = Memory::where('user_id', $this->user->id)
                ->where('memory_date', '<', now()->subYear())
                ->whereNotNull('image_path')
                ->orderBy('memory_date', 'desc')
                ->limit(10 - $memories->count())
                ->get();

            $memories = $memories->merge($additionalMemories);
        }

        // If still not enough, include memories without images
        if ($memories->count() < 3) {
            $textMemories = Memory::where('user_id', $this->user->id)
                ->whereNull('image_path')
                ->orderBy('memory_date', 'desc')
                ->limit(5)
                ->get();

            $memories = $memories->merge($textMemories);
        }

        return $memories->map(function ($memory) {
            return [
                'id' => $memory->id,
                'title' => $memory->title,
                'description' => $memory->description,
                'memory_date' => $memory->memory_date->format('Y-m-d'),
                'image_url' => $memory->image_path ? asset('storage/' . $memory->image_path) : null,
                'tags' => $memory->tags,
            ];
        })->toArray();
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Birthday surprise job failed permanently', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts
        ]);

        // You could send a notification to admins about the failure
        // or implement a fallback mechanism here
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'user:' . $this->user->id,
            'birthday-surprise',
            'birthday:' . now()->format('Y-m-d')
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [30, 60, 120]; // Wait 30s, then 1m, then 2m between retries
    }
}