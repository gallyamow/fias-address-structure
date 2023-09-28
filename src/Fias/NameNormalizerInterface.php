<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias;

interface NameNormalizerInterface
{
    public function normalize(?string $value): ?string;
}
