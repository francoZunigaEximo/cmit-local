<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsignarProfesionalEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $profesional;

    public function __construct($profesional)
    {
        $this->profesional = $profesional;
    }

    public function broadcastOn()
    {
        return new Channel('asignar-profesional');
    }

    public function broadcastAs()
    {
        return 'AsignarProfesionalEvent';
    }

    public function broadcastWith()
    {
        return [
            'profesional' => $this->profesional
        ];
    }
}
