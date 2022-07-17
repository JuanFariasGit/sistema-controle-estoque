<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
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

        if ($request->hasFile('photo') && $_FILES['photo']['name'] != "padrao.png") {
            $ext = $dados['photo']->extension();
            $imageName = time() . '.' . $ext;
            $dados['photo']->storeAs('produtos', $imageName, 'upload');
            $dados['photo'] = $imageName;
        }

        $dados['current_stock'] = 0;

        User::find(Auth::id())->products()->create($dados);

        return redirect()
            ->action('ProductController@index');
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
            
            if ($request->hasFile('photo') && $_FILES['photo']['name'] != "padrao.png") {
                $ext = $dados['photo']->extension();
                $imageName = time() . '.' . $ext;
                $dados['photo']->storeAs('produtos', $imageName, 'upload');
                $dados['photo'] = $imageName;
            } else {
                $dados['photo'] = '';
            }

            Storage::disk('upload')->delete('produtos/'.$product->photo);

            $product->update($dados);

            return redirect()
                ->action('ProductController@index');
        }

        abort('404');
    }

    public function delete($id)
    {
        $product = User::find(Auth::id())->products()->find($id);

        if ($product) {
            $movementProducts = $product->movements;

            foreach ($movementProducts as $movementProduct) {
                $movement = Movement::find($movementProduct->pivot->movement_id);
                $movement['total'] -= $movementProduct->pivot->quantity * $movementProduct->pivot->value;
                $movement->update();
            }
            Storage::disk('upload')->delete('produtos/'.$product->photo);
            $product->delete();
        }
    }

    public function downloadPhoto($id) 
    {
        $product = User::find(Auth::id())->products()->find($id);
        $imageName = $product->name.'_'.$product->photo;
        return Storage::disk('upload')->download('produtos/'.$product->photo, $imageName);
    }
}
