<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\TypeAddressLevelSpecResolverInterface;

class HouseAddressLevelSpecResolver implements TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::HOUSE;

        switch ($typeId) {
            case 1:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'владение',
                    'влд.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 2:
                return new AddressLevelSpec($currAddressLevel, 'дом', 'д.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 3:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'домовладение',
                    'двлд.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 4:
                return new AddressLevelSpec($currAddressLevel, 'гараж', 'гар.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 5:
                return new AddressLevelSpec($currAddressLevel, 'здание', 'зд.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 6:
                return new AddressLevelSpec(
                    $currAddressLevel, 'шахта', 'шахта', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 7:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'строение',
                    'стр.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 8:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'сооружение',
                    'соор.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 9:
                // было литера
                return new AddressLevelSpec(
                    $currAddressLevel, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 10:
                // было к.
                return new AddressLevelSpec(
                    $currAddressLevel, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 11:
                return new AddressLevelSpec(
                    $currAddressLevel, 'подвал', 'подв.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 12:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'котельная',
                    'кот.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 13:
                return new AddressLevelSpec(
                    $currAddressLevel, 'погреб', 'погр.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 14:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'объект незавершенного строительства',
                    'ОНС',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withValue($currAddressLevel, $typeId, 'house_types');
        }
    }
}