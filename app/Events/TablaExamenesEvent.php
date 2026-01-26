<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TablaExamenesEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tablaExamenes;

    /**
     * Create a new event instance.
     */
    public function __construct($tablaExamenes)
    {
        $this->tablaExamenes = $tablaExamenes;
    }

    public function broadcastOn()
    {
        return new Channel('actualizar-tablaExamenes');
    }

    public function broadcastAs()
    {
        return 'TablaExamenesEvent';
    }

    public function broadcastWith()
    {
        return [
            'tablaExamenes' => $this->tablaExamenes
        ];
    }
}
