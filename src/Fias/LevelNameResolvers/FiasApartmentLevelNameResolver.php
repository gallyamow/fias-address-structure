<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Fias\LevelNameResolvers;

use CoreExtensions\AddressRepository\Exceptions\LevelNameNotFoundException;
use CoreExtensions\AddressRepository\Fias\FiasLevelNameResolverInterface;
use CoreExtensions\AddressRepository\LevelName;

/**
 * @see gar.apartment_types
 */
class FiasApartmentLevelNameResolver implements FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName
    {
        switch ($typeId) {
            case 1:
                // было помещ.
                return new LevelName('помещение', 'пом.');
            case 2:
                return new LevelName('квартира', 'кв.');
            case 3:
                return new LevelName('офис', 'офис');
            case 4:
                // было ком.
                return new LevelName('комната', 'комн.');
            case 5:
                return new LevelName('рабочий участок', 'раб.уч.');
            case 6:
                return new LevelName('склад', 'скл.');
            case 7:
                return new LevelName('торговый зал', 'торг. зал');
            case 8:
                return new LevelName('цех', 'цех');
            case 9:
                return new LevelName('павильон', 'пав.');
            case 10:
                return new LevelName('подвал', 'подв.');
            case 11:
                return new LevelName('котельная', 'кот.');
            case 12:
                // было п-б
                return new LevelName('погреб', 'погр.');
            case 13:
                // было г-ж
                return new LevelName('гараж', 'гар.');
            default:
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('apartment_types', $typeId);
        }
    }
}
