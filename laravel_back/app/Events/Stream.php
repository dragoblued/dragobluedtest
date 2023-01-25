<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Stream
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;

    /**
     * Create a new event instance.
     *
     * @param int $streamId
     * @param int $streamKey
     */
    public function __construct(int $streamId, int $streamKey)
    {
        $this->stream = [
            'id' => $streamId,
            'key' => $streamKey,
        ];
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('stream.'.$this->stream['id'].'.'.$this->stream['key']);
    }
}
