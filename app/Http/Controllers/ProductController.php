<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Movement;
use App\User;

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

        if ($request->hasFile('photo')) {
            $ext = $dados['photo']->extension();
            $imageName = time().'.'.$ext;
            $dados['photo']->move(public_path('media/images'), $imageName);
            $dados['photo'] = asset('media/images').'/'.$imageName;
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

            if ($request->hasFile('photo')) {
                $ext = $dados['photo']->extension();
                $imageName = time().'.'.$ext;
                $dados['photo']->move(public_path('media/images'), $imageName);
                $dados['photo'] = asset('media/images').'/'.$imageName;
            }

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

            $product->delete();
        }
    }
}
