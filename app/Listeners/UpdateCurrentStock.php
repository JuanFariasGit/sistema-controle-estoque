<?php

namespace App\Listeners;

use App\Events\CurrentStockEvent;

class UpdateCurrentStock
{
    public function handle(CurrentStockEvent $event)
    {
        $products = $event->movement->products()->get();

        foreach ($products as $product) {
            $product->current_stock = $product->getQtInStock();
            $product->save();
        }
    }
}
