<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias;

class BaseNameNormalizer implements NameNormalizerInterface
{
    public function normalize(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $res = $value;

        // снт Раифское(Раифское СПТ)
        $res = preg_replace('|([\S])(\()|', '$1 $2', $res);

        // улицы вида С.Сайдашева, М.Джалиля => C. Сайдашева, М. Джалиля
        $res = preg_replace('|([.,!:?])([\S])|', '$1 $2', $res);

        // двойные пробелы
        return preg_replace('|\s+|', ' ', $res);
    }
}
