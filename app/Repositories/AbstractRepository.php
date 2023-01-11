<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function findAll()
    {
        return $this->model::all();
    }

    public function findById($id)
    {
        return $this->model::find($id);
    }

    public function findByIdRelationships($id, $functionsNameRelationships)
    {
        return $this->model::with($functionsNameRelationships)->where('id', $id)->first();
    }

    public function save($data, $id = NULL)
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }
}
