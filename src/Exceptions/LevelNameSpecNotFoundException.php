<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Exceptions;

class LevelNameSpecNotFoundException extends RuntimeException
{
    public static function withFiasRelationTypeAndTypeId(string $relationType, $typeId): self
    {
        return new static(
            \sprintf(
                'Failed to resolve LevelNameSpec for type "%s" and identifier "%s".',
                $relationType,
                $typeId
            )
        );
    }
}
