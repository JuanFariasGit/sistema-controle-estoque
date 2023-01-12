<?php

namespace App\Services;

use App\Repositories\MovementRepository;

class MovementService
{
    protected $movementRepository;

    public function __construct(MovementRepository $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    public function findAll()
    {
        return $this->movementRepository->findAll();
    }

    public function findAllRelationships($functionsNameRelationships)
    {
        return $this->movementRepository->findAllRelationships($functionsNameRelationships);
    }

    public function findById($id)
    {
        return $this->movementRepository->findById($id);
    }

    public function findByIdRelationships($id, $functionsNameRelationships)
    {
        return $this->movementRepository->findByIdRelationships($id, $functionsNameRelationships);
    }

    public function store($data) 
    {
        $data['user_id'] = auth()->id();
        $data['total'] = $this->getTotal($data['quantities'], $data['values']);
        
        return  $this->movementRepository->save($data);
    }

    public function update($data, $id) 
    {
        $data['user_id'] = auth()->id();
        $data['total'] = $this->getTotal($data['quantities'], $data['values']);

        $this->movementRepository->save($data, $id);       
    }

    private function getTotal($quantities, $values)
    {
        $total = 0;

        for ($i = 0; $i < count($quantities); $i++) {
            $values[$i] = str_replace(',', '.', $values[$i]);
            $total += intval($quantities[$i]) * floatval($values[$i]);
        }

        return $total;
    }
}