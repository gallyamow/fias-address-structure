<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Exceptions;

class EmptyLevelTypeException extends RuntimeException
{
    public static function withFieldNameAndIdentifier(string $fieldName, $identifier): self
    {
        return new static(
            \sprintf(
                'Empty level type "%s" for entity "%s".',
                $fieldName,
                $identifier
            )
        );
    }
}
