<?php

namespace App\Case1\Entity;

use App\Case1\Interfaces\RemovableInterface;
use App\Case1\Interfaces\ValidationSubjectInterface;
use DateTime;

/**
 * Тут можно было бы использовать Doctrine, но это было бы слишком просто
 */
class User implements RemovableInterface, ValidationSubjectInterface
{
    protected ?int $id = null;

    /**
     * Делаем здесь null, потому что в реальных ORM объекты могут быть гидрированы не всеми свойствами,
     * И таким образом хоть name и not null в модели мы его оставим null
     */
    protected ?string $name = null;

    protected ?string $email = null;

    protected ?DateTime $created = null;

    protected ?DateTime $deleted = null;

    protected ?string $notes = null;

    public function __construct()
    {
        $this->created = new DateTime();
    }

    public function getDeleted(): ?DateTime
    {
        return $this->deleted;
    }

    public function markDeleted(): void
    {
        $this->deleted = new DateTime();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setCreated(?DateTime $created): void
    {
        $this->created = $created;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}

