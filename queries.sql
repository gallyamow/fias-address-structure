--- с параметрами каждого relation
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
---
SELECT hr.id AS hierarchy_id,
    hr.objectid AS object_id,
    path.path_ltree,
    (-- сюда попадут и переименования
        SELECT array_to_json(array_agg(row_to_json(r2.*)))
        FROM unnest(path.path) AS pid,
            gar.adm_hierarchy AS hr2
                JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
            -- это условие не нужно, так как оно убирает переименовывания
            --AND r2.relation_is_active = 1 -- нужно ли это условие
        WHERE hr2.objectid = pid
            -- join hr2 понадобился только для этого условия
                AND hr2.isactive = 1
    ) AS parent_relations,
    (-- сюда попадут и переименования
        SELECT array_to_json(array_agg(row_to_json(params.*)))
        FROM gar.v_adm_hierarchy_actual_params AS params
        WHERE params.object_id = hr.objectid
    ) AS params
FROM gar.adm_hierarchy AS hr
         JOIN gar.v_adm_hierarchy_path AS path ON path.hierarchy_id = hr.id
    -- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND path.is_active = 1
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
    -- and path.path_ltree <@ '5705.6326.8607.30189934.97992250.98019972'::ltree and nlevel(path.path_ltree) > 4
ORDER BY hr.objectid
LIMIT 100;

-- select objectid from gar.param WHERE param.typeid = 10 AND param.changeidend = 0
-- group by objectid;


-- повторяются для каждого relation
SELECT p.object_id,
    p.path_ltree,
    r.relation_type,
    p.hierarchy_id,
    (-- сюда попадут и переименования
        SELECT array_to_json(array_agg(row_to_json(r2.*)))
        FROM unnest(p.path) AS pid,
            gar.adm_hierarchy AS hr2
                JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
            -- это условие не нужно, так как оно убирает переименовывания
            --AND r2.relation_is_active = 1 -- нужно ли это условие
        WHERE hr2.objectid = pid
            -- join hr2 понадобился только для этого условия
                AND hr2.isactive = 1
    ) AS parents
FROM gar.adm_hierarchy AS hr
    -- неактивные отношения не убираем специально (так как здесь lateral join)
         JOIN gar.v_adm_hierarchy_relation AS r ON r.hierarchy_id = hr.id
         JOIN gar.v_adm_hierarchy_path AS p ON p.hierarchy_id = hr.id
    -- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND p.is_active = 1
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
--GROUP BY hr.objectid, p.object_id, p.path_ltree, r.relation_type
ORDER BY hr.objectid
LIMIT 100;


---- lateral
SELECT p.object_id, p.path_ltree, r.relation_type, array_to_json(array_agg(row_to_json(t.*))) AS parents
FROM gar.adm_hierarchy AS hr
    -- неактивные отношения не убираем специально
         JOIN gar.v_adm_hierarchy_relation AS r ON r.hierarchy_id = hr.id
         JOIN gar.v_adm_hierarchy_path AS p ON p.hierarchy_id = hr.id
    -- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND p.is_active = 1
         JOIN LATERAL (
    -- сюда попадут и переименования
    SELECT r2.*
    FROM unnest(p.path) AS pid,
        gar.adm_hierarchy AS hr2
            JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
        -- это условие не нужно, так как оно убирает переименовывания
        --AND r2.relation_is_active = 1 -- нужно ли это условие
    WHERE hr2.objectid = pid
        -- join hr2 понадобился только для этого условия
            AND hr2.isactive = 1 ) t ON TRUE
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
GROUP BY hr.objectid, p.object_id, p.path_ltree, r.relation_type
ORDER BY hr.objectid
LIMIT 100;


-- old без фильтрации по active
SELECT p.object_id, p.path_ltree, r.relation_type, array_to_json(array_agg(row_to_json(t.*))) AS parents
FROM gar.adm_hierarchy AS hr
         JOIN gar.v_adm_hierarchy_relation AS r ON r.object_id = hr.objectid
         JOIN gar.v_adm_hierarchy_path AS p ON p.object_id = hr.objectid
         JOIN LATERAL (
    -- сюда попадут и переименования
    SELECT r2.relation_type, r2.relation_data
    FROM unnest(p.path) AS pid, gar.v_adm_hierarchy_relation r2
    WHERE r2.object_id = pid
        -- r2.object_id = ANY (p.path)

            AND r2.relation_is_active = 1 -- это условие нужно чтобы убрать дубли see objectid=6092
    ) t ON TRUE
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
GROUP BY hr.objectid, p.object_id, p.path_ltree, r.relation_type
ORDER BY hr.objectid
LIMIT 100;



SELECT array_to_json(array_agg(row_to_json(r2.*)))
FROM gar.v_adm_hierarchy_relation r2
WHERE r2.GROUP BY r2.objectid;

-- сортировка из коробки
SELECT r2.object_id, r2.relation_type, r2.relation_data
FROM unnest('{5705,6143,5512}'::int[]) AS pid, gar.v_adm_hierarchy_relation r2
WHERE r2.object_id = pid;

SELECT r2.object_id, r2.relation_type, r2.relation_data
FROM gar.v_adm_hierarchy_relation r2
WHERE r2.object_id = ANY ('{5705,6143,5512}'::int[]);

select *
from gar.v_adm_hierarchy_relation
where objectid = 5512

-- TODO: учитывать переименования

--
-- SELECT *
-- FROM unnest('{5705,6143,5512}'::int[]) AS pid ORDER BY unnest('{5705,6143,5512}'::int[])


SELECT r2.relation_type, r2.relation_data, hr2.id, hr2.objectid
FROM gar.adm_hierarchy AS hr2
         JOIN gar.v_adm_hierarchy_relation r2
ON r2.hierarchy_id = hr2.id AND r2.relation_is_active = 1 -- нужно ли это условие
WHERE hr2.objectid = ANY ('{5705,6326,6092}'::int[]) AND
    hr2.isactive = 1 -- это поле можно было бы перенести в relation (но это не совсем правильно по структуре)

SELECT t.id AS hierarchy_id,
    t.objectid AS object_id,
    t.relation_id,
    t.relation_is_active,
    t.relation_is_actual,
    CASE WHEN t.addr_obj IS NOT NULL
             THEN 'addr_obj'
         WHEN t.house IS NOT NULL
             THEN 'house'
         WHEN t.room IS NOT NULL
             THEN 'room'
         WHEN t.apartment IS NOT NULL
             THEN 'apartment'
         WHEN t.carplace IS NOT NULL
             THEN 'carplace'
         WHEN t.stead IS NOT NULL
             THEN 'stead'
    END AS relation_type,
    COALESCE(t.addr_obj, t.house, t.room, t.apartment, t.carplace, t.stead) AS relation_data
FROM (
         SELECT hr.id,
             hr.objectid,
             row_to_json(addr_obj) AS addr_obj,
             row_to_json(houses) AS house,
             row_to_json(rooms) AS room,
             row_to_json(apartments) AS apartment,
             row_to_json(carplaces) AS carplace,
             row_to_json(steads) AS stead,
             COALESCE(addr_obj.id, houses.id, rooms.id, apartments.id, carplaces.id, steads.id) AS relation_id,
             COALESCE(addr_obj.isactive, houses.isactive, rooms.isactive, apartments.isactive, carplaces.isactive,
                      steads.isactive) AS relation_is_active,
             COALESCE(addr_obj.isactual, houses.isactual, rooms.isactual, apartments.isactual, carplaces.isactual,
                      steads.isactual) AS relation_is_actual
         FROM gar.adm_hierarchy hr
                  LEFT JOIN gar.addr_obj ON addr_obj.objectid = hr.objectid
                  LEFT JOIN gar.houses ON houses.objectid = hr.objectid
                  LEFT JOIN gar.rooms ON rooms.objectid = hr.objectid
                  LEFT JOIN gar.apartments ON apartments.objectid = hr.objectid
                  LEFT JOIN gar.carplaces ON carplaces.objectid = hr.objectid
                  LEFT JOIN gar.steads ON steads.objectid = hr.objectid
     ) AS t;



select r.object_id, p.path_ltree
from gar.v_adm_hierarchy_relation r
         JOIN gar.v_adm_hierarchy_path AS p on p.object_id = r.object_id
WHERE r.relation_type = 'room'
LIMIt 10;
