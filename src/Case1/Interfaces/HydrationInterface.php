<?php

namespace App\Case1\Interfaces;

interface HydrationInterface
{
    public function hydrate(array $raw): EntityInterface;

    public function hydrateMany(array $rawRows): array;
}
