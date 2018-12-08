<?php

namespace App\Events;

use App\Models\Planrequirement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanRequirementSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requirement;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Planrequirement $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('planrequirement-saved');
    }
}
