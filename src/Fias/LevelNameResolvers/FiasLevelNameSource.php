<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Fias\LevelNameResolvers;

interface FiasLevelNameSource
{
    public function getItems(): array;
}
