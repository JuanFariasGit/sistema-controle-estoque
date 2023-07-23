<?php

namespace App\Listeners;

use App\Events\CurrentStockEvent;

class UpdateCurrentStock
{
    public function handle(CurrentStockEvent $event)
    {
        $event->product->current_stock = $event->product->getQtInStock();

        $event->product->save();
    }
}
