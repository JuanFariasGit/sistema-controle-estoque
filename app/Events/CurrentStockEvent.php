<?php

namespace App\Events;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurrentStockEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $movement;

    public function __construct(Movement $movement)
    {
        $this->movement = $movement;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
