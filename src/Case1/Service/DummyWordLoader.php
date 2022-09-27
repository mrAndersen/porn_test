<?php

namespace App\Case1\Service;

use App\Case1\Interfaces\DeniedWordLoaderInterface;

class DummyWordLoader implements DeniedWordLoaderInterface
{
    public function get(): array
    {
        return [
            'putin',
            'kremlin',
            'kabaeva',
        ];
    }
}

