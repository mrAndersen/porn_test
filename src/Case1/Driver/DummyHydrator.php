<?php

namespace App\Case1\Driver;

use App\Case1\Entity\User;
use App\Case1\Interfaces\EntityInterface;
use App\Case1\Interfaces\HydrationInterface;
use DateTime;

class DummyHydrator implements HydrationInterface
{
    public function hydrate(array $raw): EntityInterface
    {
        //Реальная гидрация выглядит по другому :)

        $user = new User();
        $user->setId($raw['id']);
        $user->setEmail($raw['email']);
        $user->setName($raw['name']);
        $user->setNotes($raw['notes'] ?? null);

        if (isset($raw['created'])) {
            $user->setCreated(DateTime::createFromFormat('Y-m-d', $raw['created']));
        }

        return $user;
    }

    public function hydrateMany(array $rawRows): array
    {
        // TODO: Implement hydrateMany() method.
    }
}
