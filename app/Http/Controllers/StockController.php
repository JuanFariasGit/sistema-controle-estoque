<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MovementRequest;
use App\Models\Movement;
use App\Models\Product;
use App\Models\MovementProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        $result = $this->validateProductMovement($dados['idProducts'], $dados['quantities'], $dados['values']);
        
        if ($result['status']) {
            return redirect()
                ->action('StockController@add')
                ->withErrors($result['validator'], 'products')
                ->withInput();
        }

        $dados['user_id'] = $request->user()->id;
        $dados['total'] = $this->getTotal($dados['quantities'], $result['valuesNumberFormat']);

        $movement = Movement::create($dados);

        $this->addProductMovement($movement->id, $dados['idProducts'], $dados['quantities'], $result['valuesNumberFormat']);

        return redirect()->route('stock.index')->with('alert', 'Movimentação adicionada com sucesso !');
    }

    public function editAction(MovementRequest $request)
    {
        $movement = Movement::where('user_id', $request->user()->id)
            ->where('id', $request->id)->first();

        if ($movement) {
            $dados = $request->all();

            $result = $this->validateProductMovement($dados['idProducts'], $dados['quantities'], $dados['values']);

            if ($result['status']) {
                return redirect()
                    ->action('StockController@edit', $dados['id'])
                    ->withErrors($result['validator'], 'products')
                    ->withInput();
            }

            $this->deleteProductMovements($movement->id);

            $dados['user_id'] = $request->user()->id;
            $dados['total'] = $this->getTotal($dados['quantities'], $result['valuesNumberFormat']);

            $movement->update($dados);

            $this->addProductMovement($movement->id, $dados['idProducts'], $dados['quantities'], $result['valuesNumberFormat']);
        }

        return redirect()->route('stock.index')->with('alert', 'Movimentação atualizada com sucesso !');
    }

    public function delete(Request $request)
    {
        $movement = Movement::where('user_id', $request->user()->id)
        ->where('id', $request->id)->first();

        if ($movement) {
            $this->deleteProductMovements($request->id);
            $movement->delete();
        }
    }

    public function viewMovement(Request $request, $id)
    {
        $movement = Movement::where('user_id', $request->user()->id)->where('id', $id)->first();
        $products = DB::select('select p.code, p.name, mp.quantity, mp.value from products p inner join movement_products mp on p.id = mp.product_id inner join movements m on m.id = mp.movement_id where m.id = ?', [$id]);
        return ['movement' => $movement, 'products' => $products];
    }

    private function validateProductMovement($idProducts, $quantities, $values)
    {
        for ($i = 0; $i < count($idProducts); $i++) {
            $valuesNumberFormat[$i] = str_replace(',', '.', $values[$i]);
            
            $input = ['product_id' => $idProducts[$i], 'quantity' => $quantities[$i], 'value' => $valuesNumberFormat[$i]];

            $rules = ['product_id' => 'required|max:100', 'quantity' => 'required|integer', 'value' => 'required|numeric'];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                return ['status' => true, 'validator' => $validator];
            }
        }
        return ['status' => false, 'valuesNumberFormat' => $valuesNumberFormat];
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
