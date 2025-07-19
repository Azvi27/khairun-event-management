<?php

namespace App\Notifications;

use App\Models\Memory;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMemoryShared extends Notification implements ShouldQueue
{
    use Queueable;

    public Memory $memory;
    public User $sharedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Memory $memory, User $sharedBy)
    {
        $this->memory = $memory;
        $this->sharedBy = $sharedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];
        
        // Add mail channel if user has email notifications enabled
        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/memories/' . $this->memory->id);
        
        return (new MailMessage)
            ->subject('New Memory Shared with You')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->sharedBy->name . ' has shared a memory with you: "' . $this->memory->title . '"')
            ->line('Memory Date: ' . $this->memory->memory_date->format('F j, Y'))
            ->when($this->memory->description, function ($mail) {
                return $mail->line('Description: ' . $this->memory->description);
            })
            ->action('View Memory', $url)
            ->line('Thank you for being part of our memory sharing community!')
            ->salutation('Best regards, The Khairun Team');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'memory_shared',
            'memory_id' => $this->memory->id,
            'memory_title' => $this->memory->title,
            'memory_date' => $this->memory->memory_date->format('Y-m-d'),
            'shared_by_id' => $this->sharedBy->id,
            'shared_by_name' => $this->sharedBy->name,
            'shared_by_avatar' => $this->sharedBy->avatar_url,
            'image_url' => $this->memory->image_path ? asset('storage/' . $this->memory->image_path) : null,
            'message' => $this->sharedBy->name . ' shared a memory with you: ' . $this->memory->title,
            'action_url' => url('/memories/' . $this->memory->id),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'memory_shared',
            'data' => $this->toDatabase($notifiable),
            'read_at' => null,
            'created_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get the channels the notification should broadcast on.
     */
    public function broadcastOn(): array
    {
        return ['user.' . $this->memory->user_id];
    }

    /**
     * Get the notification's broadcast type.
     */
    public function broadcastType(): string
    {
        return 'memory.shared';
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'notifications',
            'broadcast' => 'broadcasts',
        ];
    }

    /**
     * Get the notification's tags for queue management.
     */
    public function tags(): array
    {
        return [
            'memory:' . $this->memory->id,
            'user:' . $this->sharedBy->id,
            'notification:memory_shared'
        ];
    }
}