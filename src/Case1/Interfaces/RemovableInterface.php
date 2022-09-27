<?php

namespace App\Case1\Interfaces;

use DateTime;

interface RemovableInterface extends EntityInterface
{
    public function getDeleted(): ?DateTime;

    public function markDeleted(): void;
}
