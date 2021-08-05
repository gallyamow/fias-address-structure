<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;

class RoomAddressLevelSpecResolver implements AddressLevelSpecResolverInterface
{
    public function resolve(int $addressLevel, $identifier): AddressLevelSpec
    {
        $typeId = (int)$identifier;
        $currAddressLevel = $addressLevel;

        switch ($typeId) {
            case 0:
                // TODO: не ясно что с этим делать
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'Не определено',
                    'Не определено',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 1:
                // было ком.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'комната',
                    'комн.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 2:
                // было помещ.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'помещение',
                    'пом.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withIdentifier($addressLevel, $identifier, 'room_types');
        }
    }
}
