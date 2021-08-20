<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\RuntimeException;

class RelationLevelResolver
{
    /**
     * Поле 'level' есть только в таблице addr_obj.
     * Соответственно для остальных таблиц мы определяем его по relation_type.
     *
     * @param array $relation
     * @return int
     */
    public function resolveAddressLevel(array $relation): int
    {
        $relationType = $relation['type'];

        switch ($relationType) {
            case FiasRelationType::ADDR_OBJ:
                $fiasLevel = (int)$relation['data']['level'];

                return FiasLevel::mapAdmHierarchyToAddressLevel($fiasLevel);
            case FiasRelationType::HOUSE:
                return AddressLevel::HOUSE;
            case FiasRelationType::APARTMENT:
                return AddressLevel::FLAT;
            case FiasRelationType::ROOM:
                return AddressLevel::ROOM;
            case FiasRelationType::CAR_PLACE:
                return AddressLevel::CAR_PLACE;
            case FiasRelationType::STEAD:
                return AddressLevel::STEAD;
        }

        throw new RuntimeException(sprintf('Failed to resolve AddressLevel by relation_type "%s"', $relationType));
    }

    /**
     * Поле 'level' есть только в таблице addr_obj.
     * Соответственно для остальных таблиц мы определяем его по relation_type.
     *
     * @param array $relation
     * @return int
     */
    public function resolveFiasLevel(array $relation): int
    {
        $relationType = $relation['type'];

        switch ($relationType) {
            case FiasRelationType::ADDR_OBJ:
                return (int)$relation['data']['level'];
            case FiasRelationType::HOUSE:
                return FiasLevel::BUILDING;
            case FiasRelationType::APARTMENT:
                return FiasLevel::PREMISES;
            case FiasRelationType::ROOM:
                return FiasLevel::PREMISES_WITHIN_THE_PREMISES;
            case FiasRelationType::CAR_PLACE:
                return FiasLevel::CAR_PLACE;
            case FiasRelationType::STEAD:
                return FiasLevel::STEAD;
        }

        throw new RuntimeException(sprintf('Failed to resolve FiasLevel by relation_type "%s"', $relationType));
    }
}
