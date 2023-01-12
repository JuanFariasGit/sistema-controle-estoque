<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MovementRequest;
use App\Services\MovementProductService;
use App\Services\MovementService;
use App\Services\ProductService;

class StockController extends Controller
{
    private $productService;
    private $movementService;
    private $movementProductService;

    public function __construct(
        ProductService $productService,
        MovementService $movementService,
        MovementProductService $movementProductService)
    {
        $this->middleware(['auth']);
        $this->productService = $productService;
        $this->movementService = $movementService;
        $this->movementProductService = $movementProductService;
    }

    public function index()
    {
        $movements = $this->movementService->findAll();

        return view('stock.index', ['movements' => $movements]);
    }

    public function add()
    {
        $products = $this->productService->findAll();

        return view('stock.add', ['products' => $products]);
    }

    public function edit($id)
    {
        $movement = $this->movementService->findByIdRelationships($id, 'products');

        if ($movement) {
            $this->authorize('user-movement', $movement);
            
            $products = $this->productService->findAll();
            
            return view('stock.edit', ['products' => $products, 'movement' => $movement]);
        }

        abort('404');
    }

    public function addAction(MovementRequest $request)
    {
        $data = $request->all();

        $result = $this->movementProductService->validate($data['idProducts'], $data['quantities'], $data['values']);
        
        if ($result['status']) {
            return redirect()
                ->action('StockController@add')
                ->withErrors($result['validator'], 'products')
                ->withInput();
        }

        $movement = $this->movementService->store($data);

        $this->movementProductService->createOrUpdate($movement['id'], $data['idProducts'], $data['quantities'], $data['values']);

        return redirect()->route('stock.index')->with('alert', 'Movimentação adicionada com sucesso !');
    }

    public function editAction(MovementRequest $request)
    {
        $movement = $this->movementService->findById($request->id);

        if ($movement) {
            $this->authorize('user-movement', $movement);

            $data = $request->all();

            $result = $this->movementProductService->validate($data['idProducts'], $data['quantities'], $data['values']);

            if ($result['status']) {
                return redirect()
                    ->action('StockController@edit', $data['id'])
                    ->withErrors($result['validator'], 'products')
                    ->withInput();
            }

            $this->movementService->update($data, $movement['id']);

            $this->movementProductService->createOrUpdate($movement['id'], $data['idProducts'], $data['quantities'], $data['values']);
        }

        return redirect()->route('stock.index')->with('alert', 'Movimentação atualizada com sucesso !');
    }

    public function delete(Request $request)
    {
        $movement = $this->movementService->findById($request->id);

        if ($movement) {
            $this->authorize('user-movement', $movement);

            $this->movementProductService->delete($request->id);
            $movement->delete();
        }
    }

    public function viewMovement($id)
    {
        $movement = $this->movementService->findByIdRelationships($id, 'products');
        
        if ($movement) {
            $this->authorize('user-movement', $movement);

            $products = $movement->products(); 
            
            return ['movement' => $movement, 'products' => $products];
        }
        
        abort(404);
    }
}
