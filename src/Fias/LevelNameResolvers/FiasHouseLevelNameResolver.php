<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasLevelNameResolverInterface;
use Addresser\AddressRepository\LevelName;

class FiasHouseLevelNameResolver implements FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName
    {
        switch ($typeId) {
            case 1:
                return new LevelName('владение', 'влд.');
            case 2:
                return new LevelName('дом', 'д.');
            case 3:
                return new LevelName('домовладение', 'двлд.');
            case 4:
                return new LevelName('гараж', 'гар.');
            case 5:
                return new LevelName('здание', 'зд.');
            case 6:
                return new LevelName('шахта', 'шахта');
            case 7:
                return new LevelName('строение', 'стр.');
            case 8:
                return new LevelName('сооружение', 'соор.');
            case 9:
                // было литера
                return new LevelName('литера', 'лит.');
            case 10:
                // было к.
                return new LevelName('корпус', 'корп.');
            case 11:
                return new LevelName('подвал', 'подв.');
            case 12:
                return new LevelName('котельная', 'кот.');
            case 13:
                return new LevelName('погреб', 'погр.');
            case 14:
                return new LevelName('объект незавершенного строительства,ОНС', 'ОНС');
            default:
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('house_types', $typeId);
        }
    }
}