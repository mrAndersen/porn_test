<?php

namespace App\Case1\Service;

use App\Case1\Interfaces\DeniedDomainLoaderInterface;

class DummyDomainLoader implements DeniedDomainLoaderInterface
{
    public function get(): array
    {
        return [
            'mail.ru',
            'vk.com',
        ];
    }
}
