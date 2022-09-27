<?php

namespace App\Case1\Interfaces;

interface DatabaseDriverInterface
{
    public function select(string $fqcn, int $id): ?EntityInterface;

    public function selectBy(string $fqcn, array $condition): array;
}
