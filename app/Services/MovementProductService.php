<?php

namespace App\Services;

use App\Events\CurrentStockEvent;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProductRepository;
use App\Repositories\MovementRepository;
use Illuminate\Support\Facades\Validator;

class MovementProductService
{
    protected $productRepository;
    protected $movementRepository;

    public function __construct(
        ProductRepository $productRepository,
        MovementRepository $movementRepository
    ) {
        $this->productRepository = $productRepository;
        $this->movementRepository = $movementRepository;
    }

    public function validate($idProducts, $quantities, $values)
    {
        for ($i = 0; $i < count($idProducts); $i++) {
            $values[$i] = str_replace(',', '.', str_replace('.', '', $values[$i]));

            $input = ['product_id' => $idProducts[$i], 'quantity' => $quantities[$i], 'value' => $values[$i]];

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
        DB::transaction(function () use ($movementId, $productsId, $quantities, $values) {
            $movement = $this->movementRepository->findById($movementId);
            $products = $movement->products();
            $products->sync([]);

            for ($i = 0; $i < count($quantities); $i++) {
                $values[$i] = str_replace(',', '.', str_replace('.', '', $values[$i]));

                $products->attach(
                    [
                        $productsId[$i] => [
                            'quantity' => $quantities[$i],
                            'value' => floatval($values[$i])
                        ]
                    ]
                );
            }

            CurrentStockEvent::dispatch($movement);
        });

    }
}
