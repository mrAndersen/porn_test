<?php

namespace App\Case1\Interfaces;

interface ValidatorInterface
{
    /**
     * Хотим точно знать какие ошибки, если пусто - ошибок нет
     */
    public function getErrors(ValidationSubjectInterface $entity): array;

    /**
     * Сахарок
     */
    public function isValid(ValidationSubjectInterface $entity): bool;

    /**
     * Поддерживает ли валидатор данный конкретный класс
     */
    public function supports(string $fqcn): bool;
}
