<?php

namespace App\Http\Controllers;

use App\Services\MovementService;

class HomeController extends Controller
{
    private $movementService;

    public function __construct(MovementService $movementService)
    {
        $this->middleware(['auth']);
        $this->movementService = $movementService;
    }

    public function index()
    {
        $movementEntry = $this->movementService->findAllRelationships('products')->where('type', 'entry');
            
        $movementExit =  $this->movementService->findAllRelationships('products')->where('type', 'exit');
            
        $entryPie = [];
        $entryTotal = 0;
        $exitPie = [];
        $exitTotal = 0;
    
        $movementEntry->each(function($movement) use(&$entryPie, &$entryTotal) {
            $this->authorize('user-movement', $movement);

            foreach ($movement->products as $product) {
                $entryPie[ $product['name'] ] = $product['pivot']['quantity'];
                $entryTotal += intVal($product['pivot']['quantity']);
            }
        });

        $entryLabels = json_encode(array_keys($entryPie));
        $entryValues = json_encode(array_values($entryPie));
        
        $movementExit->each(function($movement) use(&$exitPie, &$exitTotal) {
            $this->authorize('user-movement', $movement);

            foreach ($movement->products as $product) {
                $exitPie[ $product['name'] ] = $product['pivot']['quantity'];
                $exitTotal += intVal($product['pivot']['quantity']);
            }
        });

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
