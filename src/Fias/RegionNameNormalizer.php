<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias;

class RegionNameNormalizer implements NameNormalizerInterface
{
    public function normalize(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if ('Чувашская Республика -' === $value) {
            return 'Чувашская Республика';
        }

        return $value;
    }
}
