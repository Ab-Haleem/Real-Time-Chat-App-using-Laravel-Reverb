<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;

    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function broadcastWith(): array
    {
        return [
            'user' => [
                'name' => $this->user->name,
            ],
            'message' => [
                'content' => $this->message->message,
                'sender_id' => $this->message->user_id,
            ]
        ];
    }

    public function broadcastOn()
    {
        return new Channel('chat');
    }
}
