<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias\AddressLevelSpecResolvers;

use Addresser\FiasAddressStructure\AddressLevel;
use Addresser\FiasAddressStructure\AddressLevelSpec;
use Addresser\FiasAddressStructure\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\FiasAddressStructure\Fias\TypeAddressLevelSpecResolverInterface;

/**
 * Комната и помещение - пересекаются с ROOM (потому что в ФИАС они могут быть как в квартире, так и вне ее).
 * Гараж, погреб, подвал - пересекаются с HOUSE (потому что в ФИАС они могут быть как в доме, так и вне его).
 *
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
                    $currAddressLevel,
                    'квартира',
                    'кв.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
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
                    $currAddressLevel,
                    'подвал',
                    'подв.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
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
                    $currAddressLevel,
                    'погреб',
                    'погр.',
                    AddressLevelSpec::NAME_POSITION_BEFORE
                );
            case 13:
                // было г-ж
                return new AddressLevelSpec($currAddressLevel, 'гараж', 'гар.', AddressLevelSpec::NAME_POSITION_BEFORE);
            default:
                throw AddressLevelSpecNotFoundException::withValue($currAddressLevel, $typeId, 'apartment_types');
        }
    }
}
