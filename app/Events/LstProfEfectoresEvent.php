<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LstProfEfectoresEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $efectores;

    public function __construct($efectores)
    {
        $this->efectores = $efectores;
    }

    public function broadcastOn()
    {
        return new Channel('listado-efectores-online');
    }

    public function broadcastAs()
    {
        return 'LstProfEfectoresEvent';
    }

    public function broadcastWith()
    {
        return [
            'efectores' => $this->efectores
        ];
    }
}
