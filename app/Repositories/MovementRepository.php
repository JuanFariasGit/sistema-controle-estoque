<?php

namespace App\Repositories;

use App\Models\Movement;

class MovementRepository extends AbstractRepository
{
    protected $movement;

    public function __construct(Movement $movement)
    {
        parent::__construct($movement);
    }
}
