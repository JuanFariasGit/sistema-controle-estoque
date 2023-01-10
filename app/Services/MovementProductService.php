<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\MovementRepository;
use Illuminate\Support\Facades\Validator;

class MovementProductService
{
    protected $productRepository;
    protected $movementRepository;

    public function __construct(
        ProductRepository $productRepository, 
        MovementRepository $movementRepository)
    {
        $this->productRepository = $productRepository;
        $this->movementRepository = $movementRepository;
    }

    public function validate($idProducts, $quantities, $values)
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

        return ['status' => false];
    }

    public function createOrUpdate($movementId, $productsId, $quantities, $values)
    {
        $movement = $this->movementRepository->findById($movementId);
        $products = $movement->products()->get()->pluck('id')->toArray();

        for ($i = 0; $i < count($quantities); $i++) {
            $prevQuantity = 0;
            $product = $this->productRepository->findById($productsId[$i]);
            $values[$i] = str_replace(',', '.', $values[$i]);
            
            if (in_array($productsId[$i], $products)) {
                $prevQuantity = $product->current_stock;
                $movement->products()->updateExistingPivot($productsId[$i], [
                    'quantity' => $quantities[$i],
                    'value' => floatval($values[$i])
                ]);
            } else {
                $movement->products()->attach($productsId[$i], [
                    'quantity' => $quantities[$i],
                    'value' => floatval($values[$i])
                ]);
            }

            if ($movement->type == 'entry') {
                $product->current_stock += intval($quantities[$i]);
            } else {
                $product->current_stock -= intval($quantities[$i]);
            }

            $product->current_stock = $product->current_stock - $prevQuantity;

            $product->update();
        }
    }

    public function delete($id)
    {
        $movement = $this->movementRepository->findByIdRelationships($id, 'products');

        foreach ($movement->products as $product) {
            
            if ($movement->type == 'entry') {
                $product->current_stock -= intval($product->pivot['quantity']);
            } else {
                $product->current_stock += intval($product->pivot['quantity']);
            }

            $product->update();
            $movement->products()->detach($product['id']);
        }

    }
}
