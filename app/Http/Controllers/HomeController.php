<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MovementProduct;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $productMovementEntry = MovementProduct::select('products.name', 'movement_products.quantity')
        ->join('movements', 'movement_products.movement_id', 'movements.id')
        ->join('products', 'movement_products.product_id', 'products.id')->where('movements.type', 'entry')
        ->where('movements.user_id', $request->user()->id)->groupBy('products.name', 'movement_products.quantity')->get();

        $productMovementExit = MovementProduct::select('products.name', 'movement_products.quantity')
        ->join('movements', 'movement_products.movement_id', 'movements.id')
        ->join('products', 'movement_products.product_id', 'products.id')->where('movements.type', 'exit')
        ->where('movements.user_id', $request->user()->id)->groupBy('products.name', 'movement_products.quantity')->get();

        $entryPie = [];
        $entryTotal = 0;
        $exitPie = [];
        $exitTotal = 0;

        foreach ($productMovementEntry as $pme) {
            $entryPie[ $pme['name'] ] = $pme['quantity'];
            $entryTotal += intval($pme['quantity']);
        }

        $entryLabels = json_encode(array_keys($entryPie));
        $entryValues = json_encode(array_values($entryPie));

        foreach ($productMovementExit as $pme) {
            $exitPie[ $pme['name'] ] = $pme['quantity'];
            $exitTotal += intVal($pme['quantity']);
        }

        $exitLabels = json_encode(array_keys($exitPie));
        $exitValues = json_encode(array_values($exitPie));

        return view('home', [
            'entryLabels' => $entryLabels,
            'entryValues' => $entryValues,
            'entryTotal' => $entryTotal,
            'exitLabels' => $exitLabels,
            'exitValues' => $exitValues,
            'exitTotal' => $exitTotal
        ]);
    }
}
