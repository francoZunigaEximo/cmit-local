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

class LstProfCombinadoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $combinados;

    public function __construct($combinados)
    {
        $this->combinados = $combinados;
    }

    public function broadcastOn()
    {
        return new Channel('listado-combinados');
    }

    public function broadcastAs()
    {
        return 'LstProfCombinadoEvent';
    }

    public function broadcastWith()
    {
        return [
            'combinados' => $this->combinados
        ];
    }
}
