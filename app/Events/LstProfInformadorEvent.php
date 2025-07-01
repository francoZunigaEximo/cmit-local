<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LstProfInformadorEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $informadores;

    public function __construct($informadores)
    {
        $this->informadores = $informadores;
    }

    public function broadcastOn()
    {
        new PrivateChannel('listado-informadores');   
    }

        public function broadcastAs()
    {
        return 'LstProfInformadorEvent';
    }

    public function broadcastWith()
    {
        return [
            'informadores' => $this->informadores
        ];
    }
}
