<?php

namespace App\Case1\Interfaces;

interface UpdatableInterface extends EntityInterface
{
    public function markUpdated(): void;
}
