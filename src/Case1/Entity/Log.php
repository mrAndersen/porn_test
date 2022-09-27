<?php

namespace App\Case1\Entity;

use App\Case1\Interfaces\UpdatableInterface;
use DateTime;

class Log implements UpdatableInterface
{
    protected ?int $id = null;

    protected ?string $fqcn = null;

    protected ?DateTime $updated = null;

    /**
     * Разница с последней ревизией
     *
     * ['field1' => 'value1', 'field2' => 'value2']
     */
    protected ?array $diff = [];


    public function getDiff(): ?array
    {
        return $this->diff;
    }

    public function setDiff(?array $diff): void
    {
        $this->diff = $diff;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function markUpdated(): void
    {
        $this->updated = new DateTime();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
