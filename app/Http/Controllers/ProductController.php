<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Movement;
use Illuminate\Http\Request;
use App\Models\MovementProduct;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('product.index');
    }

    public function findAll()
    {
        $products = User::find(Auth::id())->products()->get();

        return ['data' => $products];
    }

    public function add()
    {
        return view('product.add');
    }

    public function addAction(ProductRequest $request)
    {
        $dados = $request->all();

        if ($request->hasFile('photo')) {
            $ext = $dados['photo']->extension();
            $imageName = time() . '.' . $ext;
            $dados['photo']->storeAs('produtos', $imageName);
            $dados['photo'] = $imageName;
        }

        $dados['current_stock'] = 0;

        User::find(Auth::id())->products()->create($dados);

        return redirect()->route('product.index')->with("alert", "O Produto foi adicionado com sucesso !");
    }

    public function edit($id)
    {
        $product = User::find(Auth::id())->products()->find($id);

        if ($product) {
            return view('product.edit', ['product' => $product]);
        }

        abort('404');
    }

    public function editAction(ProductRequest $request, $id)
    {
        $product = User::find(Auth::id())->products()->find($id);

        if ($product) {
            $dados = $request->all();

            if ($request->hasFile('photo')) {
                $ext = $dados['photo']->extension();
                $imageName = time() . '.' . $ext;
                $dados['photo']->storeAs('produtos', $imageName);
                $dados['photo'] = $imageName;
                Storage::delete('produtos/' . $product->photo);
            }

            $product->update($dados);

            return redirect()->route('product.index')->with("alert", "O Produto de código {$product->code} foi atualizado com sucesso !");
        }

        abort('404');
    }

    public function delete(Request $request)
    {
        $product = User::find(Auth::id())->products()->find($request->id);

        if ($product) {
            $movements = $product->movements;

            foreach ($movements as $m) {
                $movement = Movement::find($m->pivot->movement_id);
                $movement['total'] -= $m->pivot->quantity * $m->pivot->value;
                $movement->update();
                MovementProduct::find($m->pivot->id)->delete();
            }
            Storage::delete('produtos/' . $product->photo);
            $product->delete();
        }
    }

    public function deletePhoto(Request $request)
    {
        $product = User::find(Auth::id())->products()->find($request->id);
        if ($product) {
            Storage::delete('produtos/' . $product->photo);
            $product['photo'] = '';
            $product->update();
        }
    }

    public function downloadPhoto($id)
    {
        $product = User::find(Auth::id())->products()->find($id);
        if (!empty($product->photo)) {
            $imageName = $product->name . '_' . $product->photo;
            return Storage::download('produtos/' . $product->photo, $imageName);
        } 
        abort('404');
    }
}
