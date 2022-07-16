<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MovementRequest;
use App\Models\Movement;
use App\Models\Product;
use App\Models\MovementProduct;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('stock.index');
    }

    public function findAll()
    {
        $movements = User::find(Auth::id())->movements()->get();

        return ['data' => $movements];
    }

    public function add(Request $request)
    {
        $products = User::find(Auth::id())->products()->get();

        return view('stock.add', ['products' => $products]);
    }

    public function edit(Request $request, $id)
    {
        $movement = User::find(Auth::id())->movements()->find($id);

        if ($movement) {
            $products = User::find(Auth::id())->products()->get();

            $movementProducts = MovementProduct::where('movement_id', $id)->get();

            return view('stock.edit', ['products' => $products, 'movement' => $movement, 'movementProducts' => $movementProducts]);
        }
        abort('404');
    }

    public function addAction(MovementRequest $request)
    {
        $dados = $request->all();

        for ($i = 0; $i < count($dados['idProducts']); $i++) {
            $dados['values'][$i] = str_replace(',', '.', $dados['values'][$i]);

            $input = ['product_id' => $dados['idProducts'][$i], 'quantity' => $dados['quantities'][$i],
            'value' => $dados['values'][$i]];
            $rules = ['product_id' => 'required|max:100', 'quantity' => 'required|integer', 'value' => 'required|numeric'];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                return redirect()
                ->action('StockController@add')
                ->withErrors($validator, 'products')
                ->withInput();
            }
        }

        $dados['user_id'] = $request->user()->id;
        $dados['total'] = $this->getTotal($dados['quantities'], $dados['values']);

        $movement = Movement::create($dados);

        $this->addProductMovement($movement->id, $dados['idProducts'], $dados['quantities'], $dados['values']);

        return redirect()
        ->action('StockController@index');
    }

    public function editAction(MovementRequest $request)
    {
        $movement = Movement::where('user_id', $request->user()->id)
        ->where('id', $request->id)->first();

        if ($movement) {
            $dados = $request->all();

            for ($i = 0; $i < count($dados['idProducts']); $i++) {
                $dados['values'][$i] = str_replace(',', '.', $dados['values'][$i]);

                $input = ['product_id' => $dados['idProducts'][$i], 'quantity' => $dados['quantities'][$i],
                'value' => $dados['values'][$i]];
                $rules = ['product_id' => 'required|max:100', 'quantity' => 'required|integer', 'value' => 'required|numeric'];

                $validator = Validator::make($input, $rules);

                if ($validator->fails()) {
                    return redirect()
                    ->action('StockController@edit',  ['id' => $request->id])
                    ->withErrors($validator, 'products')
                    ->withInput();
                }
            }

            $this->deleteProductMovements($movement->id);

            $dados['user_id'] = $request->user()->id;
            $dados['total'] = $this->getTotal($dados['quantities'], $dados['values']);

            $movement->update($dados);

            $this->addProductMovement($movement->id, $dados['idProducts'], $dados['quantities'], $dados['values']);
        }

        return redirect()
        ->action('StockController@index');
    }

    public function delete(Request $request, $id)
    {
        $movement = Movement::where('user_id', $request->user()->id)
        ->where('id', $id)->first();

        if ($movement) {
            $this->deleteProductMovements($id);
            $movement->delete();
        }
    }

    private function getTotal($quantities, $values)
    {
        $total = 0;

        for ($i = 0; $i < count($quantities); $i++) {
            $total += intval($quantities[$i]) * floatval($values[$i]);
        }

        return $total;
    }

    private function addProductMovement($idMovement, $idProducts, $quantities, $values)
    {
        for ($i = 0; $i < count($quantities); $i++) {
            MovementProduct::create([
                'product_id' => $idProducts[$i],
                'quantity' => $quantities[$i],
                'value' => $values[$i],
                'movement_id' => $idMovement
            ]);

            $product = Product::find($idProducts[$i]);
            $movement = Movement::find($idMovement);

            if ($movement->type == 'entry') {
                $product->current_stock += intval($quantities[$i]);
            } else {
                $product->current_stock -= intval($quantities[$i]);
            }

            $product->update();
        }
    }

    private function deleteProductMovements($id)
    {
        $movementProducts = MovementProduct::where('movement_id', $id)->get();
        $movement = Movement::find($id);

        foreach ($movementProducts as $movementProduct) {
            $product = Product::find($movementProduct['product_id']);

            if ($movement->type == 'entry') {
                $product->current_stock -= intval($movementProduct['quantity']);
            } else {
                $product->current_stock += intval($movementProduct['quantity']);
            }

            $product->update();
            $movementProduct->delete();
        }
    }
}
