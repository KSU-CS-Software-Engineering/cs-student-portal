<?php

namespace App\Events;

use App\Models\Blackout;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlackoutSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $blackout;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Blackout $blackout)
    {
        $this->blackout = $blackout;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('blackout-saved');
    }
}
