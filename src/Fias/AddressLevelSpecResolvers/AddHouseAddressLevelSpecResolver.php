<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers;

use Addresser\FiasAddressStructure\AddressLevel;
use Addresser\FiasAddressStructure\AddressLevelSpec;
use Addresser\FiasAddressStructure\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\FiasAddressStructure\Fias\TypeAddressLevelSpecResolverInterface;

class AddHouseAddressLevelSpecResolver implements TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::HOUSE;

        switch ($typeId) {
            case 1:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'корпус',
                    'корп.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
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
                    $currAddressLevel,
                    'сооружение',
                    'соор.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 4:
                // было литера
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'литера',
                    'лит.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withValue($currAddressLevel, $typeId, 'addhouse_types');
        }
    }
}
