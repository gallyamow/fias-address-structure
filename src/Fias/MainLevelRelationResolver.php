<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevel;

/**
 * Selects one main relation from several relation.
 * @see object_id = 11976
 */
class MainLevelRelationResolver
{
    private RelationLevelResolver $relationLevelResolver;

    public function __construct(RelationLevelResolver $relationLevelResolver)
    {
        $this->relationLevelResolver = $relationLevelResolver;
    }

    public function resolve(int $addressLevel, array $levelRelations): array
    {
        switch ($addressLevel) {
            // some AddressLevels match to several FiasLevels, so we have to choose one from relations
            case AddressLevel::TERRITORY:
                return $this->chooseFirstByFiasLevelPriority(
                    $levelRelations,
                    [
                        // снт Импульс/Станкозавод, тер гк т-14, зона Осиновый кол
                        FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE,
                        // гск Колесо, гск Восход
                        FiasLevel::ADDITIONAL_TERRITORIES_LEVEL,
                        // районы - самый низкий приоритет (таких записей не много)
                        // р-н ЖБИ, р-н Советский, Чайковка микрорайон, районы Уфы, Районы Белебея
                        FiasLevel::INTRACITY_LEVEL,
                    ]
                );
            case AddressLevel::STREET:
                return $this->chooseFirstByFiasLevelPriority(
                    $levelRelations,
                    [
                        // ул Привокзальная, пер Центральный
                        FiasLevel::ROAD_NETWORK_ELEMENT,
                        // ул 11 Линия, а/я Рябиновая
                        FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES,
                        // нет
                        FiasLevel::COUNTY_LEVEL,
                    ]
                );
            // otherwise we only choose the newest item
            default:
                return $this->chooseNewest($levelRelations, 'startdate');
        }
    }

    private function chooseFirstByFiasLevelPriority(array $relations, array $priority): array
    {
        foreach ($priority as $priorityFiasLevel) {
            foreach ($relations as $relation) {
                if ($this->relationLevelResolver->resolveFiasLevel($relation) === $priorityFiasLevel) {
                    return $relation;
                }
            }
        }

        throw new \RuntimeException('Failed to find main relation');
    }

    private function chooseNewest(array $relations, $dataField): array
    {
        usort($relations, fn($a, $b) => $a['data'][$dataField] > $b['data'][$dataField] ? -1 : 1);

        return $relations[0];
    }
}
