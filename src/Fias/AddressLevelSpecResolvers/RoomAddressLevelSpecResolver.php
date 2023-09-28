<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers;

use Addresser\FiasAddressStructure\AddressLevel;
use Addresser\FiasAddressStructure\AddressLevelSpec;
use Addresser\FiasAddressStructure\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\FiasAddressStructure\Fias\TypeAddressLevelSpecResolverInterface;

class RoomAddressLevelSpecResolver implements TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::ROOM;

        switch ($typeId) {
            case 1:
                // было ком.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'комната',
                    'комн.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 0: // было "Не определено", используем вместо этого "Помещение"
            case 2:
                // было помещ.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'помещение',
                    'пом.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withValue($currAddressLevel, $typeId, 'room_types');
        }
    }
}
