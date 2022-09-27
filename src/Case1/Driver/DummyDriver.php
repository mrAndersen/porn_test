<?php

namespace App\Case1\Driver;

use App\Case1\Entity\User;
use App\Case1\Interfaces\DatabaseDriverInterface;
use App\Case1\Interfaces\EntityInterface;

/**
 * Обертка над PDO или иного рога драйвер для работы с базой здесь делаем селекты апдейты инзерты и гидрацию.
 * В реальной системе гидрацию так же нужно вынести в соседний класс
 */
class DummyDriver implements DatabaseDriverInterface
{
    protected array $dummyDatabase = [];


    public function __construct()
    {
        $hydrated = new User();
        $hydrated->setId(1);
        $hydrated->setName("coollady1970");
        $hydrated->setEmail("coollady_1970@yahoo.com");

        $this->dummyDatabase[] = $hydrated;
    }

    public function selectBy(string $fqcn, array $condition): array
    {
        foreach ($this->dummyDatabase as $item) {
            if ($condition['email'] === $this->dummyDatabase[0]->getEmail()) {
                return [$item];
            }
        }

        return [];
    }

    public function select(string $fqcn, int $id): ?EntityInterface
    {
        if ($id !== 1) {
            return null;
        }

        return $this->dummyDatabase[0];
    }
}
