<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param int $roomId
     * @param int $messageId
     * @param null $message
     * @param string $action
     * @param bool $shouldBroadcastToCurrentUser
     */
    public function __construct(int $roomId, int $messageId, $message = null, $action = 'add', bool $shouldBroadcastToCurrentUser = false)
    {
        $this->message = [
           'room_id' => $roomId,
           'id' => $messageId,
           'message' => $message,
           'action' => $action
        ];
        if (!$shouldBroadcastToCurrentUser) {
            $this->dontBroadcastToCurrentUser();
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('room.'.$this->message['room_id']);
    }
}
