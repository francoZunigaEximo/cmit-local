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

class LiberarPacientesEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liberar;

    public function __construct($liberar)
    {
        $this->liberar = $liberar;
    }

    public function broadcastOn()
    {
        return new Channel('liberar-atencion');
    }

    public function broadcastAs()
    {
        return 'LiberarPacientesEvent';
    }

    public function broadcastWith()
    {
        return [
            'liberar' => $this->liberar
        ];
    }


}
