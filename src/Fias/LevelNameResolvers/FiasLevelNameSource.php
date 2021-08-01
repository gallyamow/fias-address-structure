<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

interface FiasLevelNameSource
{
    public function getItems(): array;
}
