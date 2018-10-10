<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GroupsessionEnd extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = 0;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('groupsessionend');
    }
}
