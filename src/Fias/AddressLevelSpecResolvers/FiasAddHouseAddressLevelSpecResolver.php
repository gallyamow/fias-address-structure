<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasAddressLevelSpecResolverInterface;
use Addresser\AddressRepository\AddressLevelSpec;

class FiasAddHouseAddressLevelSpecResolver implements FiasAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::HOUSE;

        switch ($typeId) {
            case 1:
                return new AddressLevelSpec($currAddressLevel, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 2:
                return new AddressLevelSpec($currAddressLevel, 'строение', 'стр.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 3:
                return new AddressLevelSpec(
                    $currAddressLevel, 'сооружение', 'соор.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 4:
                // было литера
                return new AddressLevelSpec($currAddressLevel, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE);
            default:
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('addhouse_types', $typeId);
        }
    }
}
