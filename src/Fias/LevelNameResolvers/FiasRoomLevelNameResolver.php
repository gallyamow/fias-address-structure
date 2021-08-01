<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasLevelNameResolverInterface;
use Addresser\AddressRepository\LevelName;

class FiasRoomLevelNameResolver implements FiasLevelNameResolverInterface
{
    public function resolve(int $typeId): LevelName
    {
        switch ($typeId) {
            case 0:
                // TODO: не ясно что с этим делать
                return new LevelName('Не определено', 'Не определено');
            case 1:
                // было ком.
                return new LevelName('комната', 'комн.');
            case 2:
                // было помещ.
                return new LevelName('помещение', 'пом.');
            default:
                throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('room_types', $typeId);
        }
    }
}
