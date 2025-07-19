<?php

namespace App\Events;

use App\Models\Memory;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemoryCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Memory $memory;
    public User $user;
    public array $sharedWith;

    /**
     * Create a new event instance.
     */
    public function __construct(Memory $memory, User $user, array $sharedWith = [])
    {
        $this->memory = $memory;
        $this->user = $user;
        $this->sharedWith = $sharedWith;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        
        // Broadcast to memory creator
        $channels[] = new PrivateChannel('user.' . $this->user->id);
        
        // Broadcast to users the memory is shared with
        foreach ($this->sharedWith as $userId) {
            $channels[] = new PrivateChannel('user.' . $userId);
        }
        
        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'memory' => [
                'id' => $this->memory->id,
                'title' => $this->memory->title,
                'description' => $this->memory->description,
                'memory_date' => $this->memory->memory_date->format('Y-m-d'),
                'image_url' => $this->memory->image_path ? asset('storage/' . $this->memory->image_path) : null,
                'tags' => $this->memory->tags,
                'created_at' => $this->memory->created_at->toISOString(),
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar_url,
            ],
            'type' => 'memory_created',
            'message' => $this->user->name . ' created a new memory: ' . $this->memory->title,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'memory.created';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function shouldBroadcast(): bool
    {
        return !$this->memory->is_private || !empty($this->sharedWith);
    }
}