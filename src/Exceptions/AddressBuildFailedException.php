<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Exceptions;

class AddressBuildFailedException extends RuntimeException
{
    public static function withIdentifier(string $identifierName, $identifierValue, string $message): self
    {
        return new static(
            \sprintf(
                'Failed to build address for "%s"="%s" with message "%s".',
                $identifierName,
                $identifierValue,
                $message
            )
        );
    }
}
