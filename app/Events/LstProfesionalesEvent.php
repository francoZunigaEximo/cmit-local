<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LstProfesionalesEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $efectores;

    public function __construct($efectores)
    {
        $this->efectores = $efectores;
    }

    public function broadcastOn()
    {
        return new Channel('listado-efectores');
    }

    public function broadcastAs()
    {
        return 'LstProfesionalesEvent';
    }

    public function broadcastWith()
    {
        return [
            'efectores' => $this->efectores
        ];
    }
}
