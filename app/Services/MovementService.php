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
        return  $this->movementRepository->save($data);
    }

    public function update($id, $data) 
    {
        $this->movementRepository->save($data, $id);       
    }
}