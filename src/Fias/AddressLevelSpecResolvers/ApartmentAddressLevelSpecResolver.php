<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\TypeAddressLevelSpecResolverInterface;

/**
 * TODO: комната и помещение - пересекаются с ROOM
 * @see gar.apartment_types
 */
class ApartmentAddressLevelSpecResolver implements TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec
    {
        $currAddressLevel = AddressLevel::FLAT;

        switch ($typeId) {
            case 1:
                // было помещ.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'помещение',
                    'пом.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 2:
                return new AddressLevelSpec(
                    $currAddressLevel, 'квартира', 'кв.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 3:
                return new AddressLevelSpec($currAddressLevel, 'офис', 'офис', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 4:
                // было ком.
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'комната',
                    'комн.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 5:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'рабочий участок',
                    'раб.уч.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 6:
                return new AddressLevelSpec($currAddressLevel, 'склад', 'скл.', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 7:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'торговый зал',
                    'торг. зал',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 8:
                return new AddressLevelSpec($currAddressLevel, 'цех', 'цех', AddressLevelSpec::NAME_POSITION_BEFORE);
            case 9:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'павильон',
                    'пав.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 10:
                return new AddressLevelSpec(
                    $currAddressLevel, 'подвал', 'подв.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 11:
                return new AddressLevelSpec(
                    $currAddressLevel,
                    'котельная',
                    'кот.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 12:
                // было п-б
                return new AddressLevelSpec(
                    $currAddressLevel, 'погреб', 'погр.', AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 13:
                // было г-ж
                return new AddressLevelSpec($currAddressLevel, 'гараж', 'гар.', AddressLevelSpec::NAME_POSITION_BEFORE);
            default:
                throw AddressLevelSpecNotFoundException::withIdentifier($currAddressLevel, $typeId, 'apartment_types');
        }
    }
}
