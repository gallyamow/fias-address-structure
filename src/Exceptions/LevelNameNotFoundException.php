<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Exceptions;

class LevelNameNotFoundException extends RuntimeException
{
    public static function withFiasRelationTypeAndTypeId(string $relationType, $typeId): self
    {
        return new static(
            \sprintf(
                'Failed to resolve the name of level for relation type "%s" and typeId "%s". You need to check up new items in type table.',
                $relationType,
                $typeId
            )
        );
    }
}
