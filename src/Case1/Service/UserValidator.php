<?php

namespace App\Case1\Service;

use App\Case1\Entity\User;
use App\Case1\Interfaces\DeniedDomainLoaderInterface;
use App\Case1\Interfaces\DeniedWordLoaderInterface;
use App\Case1\Interfaces\ValidationSubjectInterface;
use App\Case1\Interfaces\ValidatorInterface;

class UserValidator implements ValidatorInterface
{
    protected ?array $deniedWords = null;

    protected ?array $deniedDomains = null;

    protected Manager $manager;

    protected DeniedDomainLoaderInterface $deniedDomainLoader;

    protected DeniedWordLoaderInterface $deniedWordLoader;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->deniedDomainLoader = new DummyDomainLoader();
        $this->deniedWordLoader = new DummyWordLoader();
        $this->manager->addValidator($this);
    }

    public function isValid(ValidationSubjectInterface $entity): bool
    {
        return $this->getErrors($entity) === [];
    }

    /**
     * @param User $entity
     */
    public function getErrors(ValidationSubjectInterface $entity): array
    {
        if ($this->deniedWords === null) {
            $this->loadDeniedWords();
        }

        if ($this->deniedDomains === null) {
            $this->loadDeniedDomains();
        }

        if (!$entity->getName()) {
            return ['name'];
        }

        if (mb_strlen($entity->getName()) < 8) {
            return ['name.length'];
        }

        if (!preg_match('@^[a-z0-9]+$@u', $entity->getName())) {
            return ['name.regex'];
        }

        foreach ($this->deniedWords as $deniedWord) {
            if (strpos(mb_strtolower($entity->getName()), mb_strtolower($deniedWord)) !== false) {
                return ['name.denied_words'];
            }
        }

        if (!$entity->getEmail()) {
            return ['email'];
        }

        if (!filter_var($entity->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return ['email.email'];
        }

        $emailDomain = explode('@', $entity->getEmail())[1] ?? null;

        if (in_array($emailDomain, $this->deniedDomains)) {
            return ['email.domain'];
        }

        //Несмотря на то, что мы делаем здесь проверку, мы всё равно должны ловить ошибки уровня UniqueConstriant
        //и реагировать соответственно
        if ($this->manager->selectBy(User::class, ['email' => $entity->getEmail()])) {
            return ['email.non_unique'];
        }

        return [];
    }

    protected function loadDeniedWords(): void
    {
        $this->deniedWords = $this->deniedWordLoader->get();
    }

    protected function loadDeniedDomains(): void
    {
        $this->deniedDomains = $this->deniedDomainLoader->get();
    }

    public function supports(string $fqcn): bool
    {
        return $fqcn === User::class;
    }
}

