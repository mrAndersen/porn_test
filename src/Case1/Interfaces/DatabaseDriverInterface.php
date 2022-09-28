<?php

namespace App\Case1\Interfaces;

use App\Case1\Exception\UniqueDatabaseException;

/**
 *
 * Обертка над PDO или иного рода драйвер для работы с базой здесь делаем селекты апдейты инзерты
 * Ясно что подобный класс работы с базой должен иметь методы для массовой вставки сущностей, которые мы опустим пока
 * Развивать этот класс можно бесконечно
 */
interface DatabaseDriverInterface
{
    public function select(string $fqcn, int $id): ?EntityInterface;

    public function selectBy(string $fqcn, array $condition): array;

    /**
     * @throws UniqueDatabaseException
     */
    public function insert(EntityInterface $entity): void;

    /**
     * @throws UniqueDatabaseException
     */
    public function update(EntityInterface $entity): void;

    public function getHydrator(): HydrationInterface;
}
