<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\AddressLevelSpec;

class AddHouseAddressLevelSpecResolver implements AddressLevelSpecResolverInterface
{
    public function resolve(int $addressLevel, $identifier): AddressLevelSpec
    {
        $typeId = (int)$identifier;
        $currAddressLevel = AddressLevel::HOUSE;

        switch ($typeId) {
            case 1:
                return new AddressLevelSpec(
                    $addressLevel, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 2:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'строение',
                    'стр.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 3:
                return new AddressLevelSpec(
                    $addressLevel, 'сооружение', 'соор.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 4:
                // было литера
                return new AddressLevelSpec(
                    $addressLevel, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withIdentifier($addressLevel, $identifier, 'addhouse_types');
        }
    }
}
