<?php

namespace App\Events;

use App\Models\User;
use App\Models\BirthdaySurprise;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BirthdaySurpriseTriggered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public BirthdaySurprise $surprise;
    public array $memories;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, BirthdaySurprise $surprise, array $memories = [])
    {
        $this->user = $user;
        $this->surprise = $surprise;
        $this->memories = $memories;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'surprise' => [
                'id' => $this->surprise->id,
                'title' => $this->surprise->title,
                'message' => $this->surprise->message,
                'surprise_type' => $this->surprise->surprise_type,
                'background_music' => $this->surprise->background_music,
                'animation_style' => $this->surprise->animation_style,
                'created_at' => $this->surprise->created_at->toISOString(),
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'birthday' => $this->user->birthday?->format('Y-m-d'),
            ],
            'memories' => array_map(function ($memory) {
                return [
                    'id' => $memory['id'],
                    'title' => $memory['title'],
                    'image_url' => $memory['image_url'],
                    'memory_date' => $memory['memory_date'],
                ];
            }, $this->memories),
            'type' => 'birthday_surprise',
            'message' => '🎉 Happy Birthday! A special surprise has been prepared for you!',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'birthday.surprise';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function shouldBroadcast(): bool
    {
        return $this->surprise->is_active;
    }
}