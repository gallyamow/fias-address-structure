<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AddressFinder implements AddressFinderInterface
{
    private AddressBuilder $addressBuilder;

    public function __construct(AddressBuilder $addressBuilder)
    {
        $this->addressBuilder = $addressBuilder;
    }

    public function find(array $options): \Iterator
    {
        $qb = $this->queryBuilder($options);

        foreach ($qb->get() as $row) {
            yield $this->addressBuilder->build($row);
        }
    }

    private function queryBuilder(array $conditions): Builder
    {
        $sql = <<<SQL
SELECT hr.id AS hierarchy_id,
    hr.objectid AS object_id,
    path.path_ltree,
    (-- сюда попадут и переименования
        SELECT array_to_json(array_agg(jsonb_build_object(
            'relation', row_to_json(r2.*),
            'params', (
               SELECT array_to_json(array_agg(row_to_json(params.*)))
               FROM gar.v_adm_hierarchy_actual_params AS params
               WHERE params.object_id = r2.object_id
           )
        )))
        FROM unnest(path.path) AS pid,
            gar.adm_hierarchy AS hr2
                JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
            -- это условие не нужно, так как оно убирает переименовывания
            --AND r2.relation_is_active = 1 -- нужно ли это условие
        WHERE hr2.objectid = pid
            -- join hr2 понадобился только для этого условия
                AND hr2.isactive = 1
    ) AS parents
FROM gar.adm_hierarchy AS hr
         JOIN gar.v_adm_hierarchy_path AS path ON path.hierarchy_id = hr.id
    -- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND path.is_active = 1
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
    -- and path.path_ltree <@ '5705.6326.8607.30189934.97992250.98019972'::ltree and nlevel(path.path_ltree) > 4
ORDER BY hr.objectid
LIMIT 1000;
SQL;

        $qb = DB::table('gar.adm_hierarchy')
            ->select(
                DB::raw(
                    '
                    hr.id AS hierarchy_id,
                    hr.objectid AS object_id,
                    path.path_ltree,
                    (
                        SELECT array_to_json(array_agg(jsonb_build_object(
                            \'relation\', row_to_json(r2.*),
                            \'params\', (
                               SELECT array_to_json(array_agg(row_to_json(params.*)))
                               FROM gar.v_adm_hierarchy_actual_params AS params
                               WHERE params.object_id = r2.object_id
                           )
                        )))
                        FROM unnest(path.path) AS pid,
                            gar.adm_hierarchy AS hr2
                                JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
                            -- это условие не нужно, так как оно убирает переименовывания
                            --AND r2.relation_is_active = 1 -- нужно ли это условие
                        WHERE hr2.objectid = pid
                            -- join hr2 понадобился только для этого условия
                                AND hr2.isactive = 1
                    ) AS parents
                    '
                )
            )
            ->join(
                'v_adm_hierarchy_path AS path',
                static function ($join) {
                    $join->on('path.hierarchy_id', '=', 'hr.id')->where('path.is_active = 1');
                }
            )
            ->where('hr.isactive', '=', 1);

        $qb = $this->handleConditions($conditions, $qb);

        // важно сортировать по ID чтобы в случае чего можно было начать c fromId
        $qb->orderBy('hr.objectid');

        return $qb;
    }

    private function handleConditions(array $conditions, Builder $qb): Builder
    {
        if (!empty($conditions['fromId'])) {
            $qb->where('hr.objectid', '>=', (int)$conditions['fromId']);
        }

        return $qb;
    }
}
