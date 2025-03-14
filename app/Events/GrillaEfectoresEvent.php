<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GrillaEfectoresEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $grilla;

    public function __construct($grilla)
    {
        $this->grilla = $grilla;
    }

    public function broadcastOn()
    {
        return new Channel('grilla-efectores');
    }

    public function broadcastAs()
    {
        return 'GrillaEfectoresEvent';
    }

    public function broadcastWith()
    {
        return [
            'grilla' => $this->grilla
        ];
    }

    
}
