<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasLevelNameResolverInterface;
use Addresser\AddressRepository\LevelName;

class FiasAddHouseLevelNameResolver implements FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName
    {
        switch ($typeId) {
            case 1:
                return new LevelName('корпус', 'корп.');
            case 2:
                return new LevelName('строение', 'стр.');
            case 3:
                return new LevelName('сооружение', 'соор.');
            case 4:
                // было литера
                return new LevelName('литера', 'лит.');
            default:
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('addhouse_types', $typeId);
        }
    }
}
