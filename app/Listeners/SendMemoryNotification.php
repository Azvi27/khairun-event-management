<?php

namespace App\Listeners;

use App\Events\MemoryCreated;
use App\Models\User;
use App\Notifications\NewMemoryShared;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendMemoryNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MemoryCreated $event): void
    {
        try {
            // Don't send notifications for private memories unless shared
            if ($event->memory->is_private && empty($event->sharedWith)) {
                return;
            }

            // Send notifications to users the memory is shared with
            if (!empty($event->sharedWith)) {
                $users = User::whereIn('id', $event->sharedWith)
                    ->where('id', '!=', $event->user->id) // Don't notify the creator
                    ->get();

                foreach ($users as $user) {
                    $user->notify(new NewMemoryShared($event->memory, $event->user));
                }

                Log::info('Memory shared notifications sent', [
                    'memory_id' => $event->memory->id,
                    'creator_id' => $event->user->id,
                    'shared_with' => $event->sharedWith,
                    'notification_count' => $users->count()
                ]);
            }

            // For public memories, you could implement additional logic here
            // such as notifying followers or friends

        } catch (\Exception $e) {
            Log::error('Failed to send memory notification', [
                'memory_id' => $event->memory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(MemoryCreated $event, \Throwable $exception): void
    {
        Log::error('Memory notification job failed permanently', [
            'memory_id' => $event->memory->id,
            'creator_id' => $event->user->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts ?? 'unknown'
        ]);
    }
}