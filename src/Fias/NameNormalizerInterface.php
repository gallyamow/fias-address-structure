<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

interface NameNormalizerInterface
{
    public function normalize(?string $value): ?string;
}
