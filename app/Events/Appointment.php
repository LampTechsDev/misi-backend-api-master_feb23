<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Appointment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var $appointment
     * @var $appointment_type
     */
    public $appointment;
    public $appointment_type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($appointment, $appointment_type = "new")
    {
        $this->appointment      = $appointment;
        $this->appointment_type = $appointment_type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
