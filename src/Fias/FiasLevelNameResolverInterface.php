<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\LevelName;

interface FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName;
}
