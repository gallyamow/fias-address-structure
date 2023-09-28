<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Exceptions;

class EmptyLevelTypeException extends RuntimeException
{
    public static function withObjectId(string $fieldName, int $objectId): self
    {
        return new static(
            \sprintf(
                'Empty level type "%s" for address ("%s").',
                $fieldName,
                $objectId
            )
        );
    }
}
