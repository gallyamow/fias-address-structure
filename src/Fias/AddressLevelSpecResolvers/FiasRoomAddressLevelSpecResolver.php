<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasAddressLevelSpecResolverInterface;
use Addresser\AddressRepository\AddressLevelSpec;

class FiasRoomAddressLevelSpecResolver implements FiasAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::ROOM;

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
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('room_types', $typeId);
        }
    }
}
