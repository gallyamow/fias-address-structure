<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

interface FiasTypeSource
{
    public function getItems(): array;
}
