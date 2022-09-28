<?php

namespace App\Case1\Driver;

use App\Case1\Interfaces\DatabaseDriverInterface;
use App\Case1\Interfaces\EntityInterface;
use App\Case1\Interfaces\HydrationInterface;

class DummyDriver implements DatabaseDriverInterface
{
    protected array $dummyDatabase = [
        [
            'id'    => 1,
            'name'  => 'coollady1970',
            'email' => 'coollady_1970@yahoo.com',
        ],
    ];

    protected HydrationInterface $hydrator;

    public function __construct()
    {
        $this->hydrator = new DummyHydrator();
    }

    public function selectBy(string $fqcn, array $condition): array
    {
        foreach ($this->dummyDatabase as $item) {
            //Реальный поиск по базе выглядит не так :)
            if ($condition['email'] === $this->dummyDatabase[0]['email']) {
                return [$this->hydrator->hydrate($item)];
            }
        }

        return [];
    }

    public function select(string $fqcn, int $id): ?EntityInterface
    {
        if ($id !== 1) {
            return null;
        }

        return $this->hydrator->hydrate($this->dummyDatabase[0]);
    }

    public function insert(EntityInterface $entity): void
    {
        return;
    }

    public function update(EntityInterface $entity): void
    {
        return;
    }

    public function getHydrator(): HydrationInterface
    {
        return $this->hydrator;
    }
}
