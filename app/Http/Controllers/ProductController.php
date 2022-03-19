<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\ProductMovement;
use App\Movement;

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
    
    public function findAll(Request $request) 
    {
        $products = Product::where('user_id', $request->user()->id)->get();
        
        echo json_encode(['data' => $products]);
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

        $dados['user_id'] = $request->user()->id;
        $dados['current_stock'] = 0;

        Product::create($dados);

        return redirect()
        ->action('ProductController@index');
    }

    public function edit(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)
        ->where('id', $id)->first();

        if ($product) {
            return view('product.edit', ['product' => $product]);  
        } 
        abort('404');
    }

    public function editAction(ProductRequest $request)
    {
        $product = Product::where('user_id', $request->user()->id)
        ->where('id', $request->id)->first();
        
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

    public function delete(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)
        ->where('id', $id)->first();
    
        if ($product) {
            $movementProducts = ProductMovement::select('product_movements.movement_id', 
            'product_movements.quantity', 'product_movements.value')->join('products', 'products.id', 
            'product_movements.product_id')->where('products.id', $id)->get(); 

            foreach ($movementProducts as $movementProduct) {
               $movement = Movement::find($movementProduct['movement_id']);
               $movement['total'] -= $movementProduct['quantity'] * $movementProduct['value'];
               $movement->update();
            }      
            
            $product->delete();
        }
    }
}
