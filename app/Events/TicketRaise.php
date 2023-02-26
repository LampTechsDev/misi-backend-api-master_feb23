<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketRaise
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var $ticket
     * @var $ticket_type
     */
    public $ticket;
    public $ticket_type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($ticket, $ticket_type = "new")
    {
        $this->ticket       = $ticket;
        $this->ticket_type  = $ticket_type;
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
