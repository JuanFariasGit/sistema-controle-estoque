<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function findAll()
    {
        return $this->productRepository->findAll();
    }

    public function findById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function store($data)
    {
        if (key_exists('photo', $data)) {
            $ext = $data['photo']->extension();
            $imageName = time() . '.' . $ext;
            $data['photo']->storeAs('produtos', $imageName);
            $data['photo'] = $imageName;
        }
        
        $data['user_id'] = auth()->id();
        
        $this->productRepository->save($data);
    }

    public function update($product, $data)
    {
        if (key_exists('photo', $data)) {
            $ext = $data['photo']->extension();
            $imageName = time() . '.' . $ext;
            $data['photo']->storeAs('produtos', $imageName);
            $data['photo'] = $imageName;
            Storage::delete('produtos/' . $product['photo']);
        }

        $data['user_id'] = auth()->id();

        $this->productRepository->save(
            $data, 
            $product['id']
        );
    }

    public function delete($id)
    {
        $product = $this->productRepository->findByIdRelationships($id, 'movements');

        if ($product) {
            $movements = $product->movements;

            foreach ($movements as $m) {
                $m->total -= ($m->pivot->quantity * $m->pivot->value);
                $m->update();
            }
            
            Storage::delete('produtos/' . $product->photo);
            $product->delete();
        }
    }

    public function deletePhoto($id)
    {
        $product = $this->productRepository->findById($id);

        if ($product) {
            Storage::delete('produtos/' . $product->photo);
            $product['photo'] = '';
            $product->update();
        }
    }

    public function downloadPhoto($id)
    {
        $product = $this->productRepository->findById($id);   
        
        if (!empty($product->photo)) {
            $imageName = $product->name . '_' . $product->photo;
            return Storage::download('produtos/' . $product->photo, $imageName);
        } 

        abort(404);
    }
}
