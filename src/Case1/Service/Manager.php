<?php

namespace App\Case1\Service;

use App\Case1\Driver\DummyDriver;
use App\Case1\Entity\Log;
use App\Case1\Exception\ManagerException;
use App\Case1\Exception\UniqueDatabaseException;
use App\Case1\Exception\ValidationException;
use App\Case1\Interfaces\DatabaseDriverInterface;
use App\Case1\Interfaces\EntityInterface;
use App\Case1\Interfaces\RemovableInterface;
use App\Case1\Interfaces\UpdatableInterface;
use App\Case1\Interfaces\ValidationSubjectInterface;
use App\Case1\Interfaces\ValidatorInterface;

/**
 * Максимально наивная реализация EntityManager
 */
class Manager
{
    protected array $buffer = [];

    protected array $log = [];

    protected int $virtual = -1;

    /** @var ValidatorInterface[] */
    protected array $validators = [];

    protected DatabaseDriverInterface $driver;

    public function __construct()
    {
        $this->driver = new DummyDriver();
    }

    public function selectBy(string $fqcn, array $condition): array
    {
        $results = $this->driver->selectBy($fqcn, $condition);

        foreach ($results as $result) {
            $this->buffer[$fqcn][$result->getId()] = $result;
        }

        return $results;
    }


    public function select(string $fqcn, int $id): ?EntityInterface
    {
        if (isset($this->selectBuffer[$fqcn][$id])) {
            return $this->selectBuffer[$fqcn][$id];
        }

        $hydrated = $this->driver->select($fqcn, $id);

        if (!$hydrated) {
            return null;
        }

        $this->buffer[$fqcn][$hydrated->getId()] = $hydrated;

        return $hydrated;
    }

    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[get_class($validator)] = $validator;
    }

    /**
     * @throws ManagerException
     */
    public function flush(): void
    {
        $this->log = [];

        foreach ($this->buffer as $fqcn => $entities) {
            foreach ($entities as $vid => $entity) {
                if ($vid < 0) {
                    try {
                        $this->driver->insert($entity);
                    } catch (UniqueDatabaseException $uniqueNaiveEntityException) {
                        //какая-либо дополнительная логика
                        throw new ManagerException("Unique constraint violation", 10000);
                    }
                } else {
                    try {
                        $this->driver->update($entity);
                    } catch (UniqueDatabaseException $uniqueNaiveEntityException) {
                        //какая-либо дополнительная логика
                        throw new ManagerException("Unique constraint violation", 10000);
                    }
                }

                // здесь мы должны получить Log разницы через diff и что-то с ним сделать, записать в БД или в elastic
                // или еще куда-то, развивать дальше это можно бесконечно
            }
        }

        $this->virtual = -1;
    }

    /**
     * @throws ManagerException
     */
    public function diff(EntityInterface $new, EntityInterface $old): ?Log
    {
        if (get_class($new) !== get_class($old)) {
            throw new ManagerException("Can not compare entities");
        }


        return null;
    }

    /**
     * @throws ManagerException
     */
    public function delete(EntityInterface $removable): void
    {
        if (!$removable->getId()) {
            throw new ManagerException("Can not remove non existing entity");
        }

        if (!$removable instanceof RemovableInterface) {
            throw new ManagerException("Can not remove non removable entity");
        }

        $this->buffer[get_class($removable)][$removable->getId()] = $removable;
        $removable->markDeleted();
    }

    /**
     * @throws ValidationException
     */
    public function persist(EntityInterface $entity): void
    {
        if ($entity instanceof UpdatableInterface) {
            $entity->markUpdated();
        }

        if ($entity instanceof ValidationSubjectInterface) {
            $this->validate($entity);
        }

        if (!$entity->getId()) {
            $vid = $this->assignVirtualId();
        } else {
            $vid = $entity->getId();
        }

        $this->buffer[get_class($entity)][$vid] = $entity;
    }

    /**
     * @throws ValidationException
     */
    protected function validate(ValidationSubjectInterface $entity)
    {
        foreach ($this->validators as $validator) {
            if ($validator->supports(get_class($entity))) {
                $errors = $validator->getErrors($entity);

                if ($errors) {
                    throw new ValidationException(
                        sprintf(
                            "Entity %s is invalid [%s]\nValidated by %s",
                            spl_object_hash($entity),
                            implode(',', $errors),
                            get_class($validator)
                        )
                    );
                }
            }
        }
    }

    protected function assignVirtualId(): int
    {
        $id = $this->virtual;
        $this->virtual -= 1;

        return $id;
    }
}
