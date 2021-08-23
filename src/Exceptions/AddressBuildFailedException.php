<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Exceptions;

class AddressBuildFailedException extends RuntimeException
{
    public static function withObjectId(string $message, int $objectId): self
    {
        return new static(
            \sprintf(
                'Failed to build address (%s) with message "%s".',
                $objectId,
                $message
            )
        );
    }
}
