<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;

class HouseAddressLevelSpecResolver implements AddressLevelSpecResolverInterface
{
    public function resolve(int $addressLevel, $identifier): AddressLevelSpec
    {
        $typeId = (int)$identifier;
        $currAddressLevel = $addressLevel;

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
                    $addressLevel,
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
                    $addressLevel, 'шахта', 'шахта', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 7:
                return new AddressLevelSpec(
                    $addressLevel,
                    'строение',
                    'стр.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 8:
                return new AddressLevelSpec(
                    $addressLevel,
                    'сооружение',
                    'соор.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 9:
                // было литера
                return new AddressLevelSpec(
                    $addressLevel, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 10:
                // было к.
                return new AddressLevelSpec(
                    $addressLevel, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 11:
                return new AddressLevelSpec(
                    $addressLevel, 'подвал', 'подв.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 12:
                return new AddressLevelSpec(
                    $addressLevel,
                    'котельная',
                    'кот.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 13:
                return new AddressLevelSpec(
                    $addressLevel, 'погреб', 'погр.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 14:
                return new AddressLevelSpec(
                    $addressLevel,
                    'объект незавершенного строительства,ОНС',
                    'ОНС',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            default:
                throw AddressLevelSpecNotFoundException::withIdentifier($addressLevel, $identifier, 'house_types');
        }
    }
}