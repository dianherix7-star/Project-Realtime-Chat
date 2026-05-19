<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserPresenceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user, public bool $isOnline)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('presence'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'   => $this->user->id,
            'is_online' => $this->isOnline,
        ];
    }
}
