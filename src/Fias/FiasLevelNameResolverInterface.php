<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Fias;

use CoreExtensions\AddressRepository\LevelName;

interface FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName;
}
