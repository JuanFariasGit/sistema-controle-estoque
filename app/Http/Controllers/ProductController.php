<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    private $service;

    public function __construct(ProductService $service)
    {
        $this->middleware(['auth']);
        $this->service = $service;
    }

    public function index()
    {
        $products = $this->service->findAll();

        return view('product.index', compact('products'));
    }

    public function add()
    {
        return view('product.add');
    }

    public function addAction(ProductRequest $request)
    {
        $data = $request->validated();

        if ($data) {
            $this->service->store($data);

            return redirect()->route('product.index')
                ->with("alert", "O Produto foi adicionado com sucesso !");
        }
    }

    public function edit($id)
    {
        $product = $this->service->findById($id);

        if ($product) {
            $this->authorize('user-product', $product);
            
            return view('product.edit', compact('product'));
        }

        abort('404');
    }

    public function editAction(ProductRequest $request, $id)
    {
        $product = $this->service->findById($id);
        
        if ($product) {
            $this->authorize('user-product', $product);
    
            $data = $request->validated();

            if ($data) {
                $this->service->update($product, $data);
            }

            return redirect()->route('product.index')
                ->with("alert", "O Produto de código {$product->code} foi atualizado com sucesso !");
        }

        abort('404');
    }

    public function delete(Request $request)
    {
        $product = $this->service->findById($request->id);

        if ($product) {
            $this->authorize('user-product', $product);
        
            $this->service->delete($product['id']);
        }
    }

    public function deletePhoto(Request $request)
    {
        $product = $this->service->findById($request->id);

        if ($product) {
            $this->authorize('user-product', $product);

            $this->service->deletePhoto($product['id']);
        }
    }

    public function downloadPhoto($id)
    {
        $product = $this->service->findById($id);
        
        if ($product) {
            $this->authorize('user-product', $product);
            
            $this->service->downloadPhoto($id);
        }

        abort('404');
    }
}
