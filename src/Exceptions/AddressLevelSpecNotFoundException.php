<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Exceptions;

class AddressLevelSpecNotFoundException extends RuntimeException
{
    public static function withValue(int $addressLevel, $value, string $message): self
    {
        return new static(
            \sprintf(
                'Failed to resolve spec for address level "%s" and identifier "%s" ("%s").',
                $addressLevel,
                $value,
                $message
            )
        );
    }
}
