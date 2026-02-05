

explain analyze
SELECT q.object_id, q.path_ltree, q.objects, q.params
FROM gar.v_indexer_queue AS q
WHERE --q.object_id > 2465883
    --AND p.path_ltree <@ '169363.169398'::ltree
    q.object_id = 76943274
ORDER BY q.object_id
LIMIT 1000;

-- итерацией по path и subquery
explain analyze
SELECT p.object_id,
    p.path_ltree,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_adm_hierarchy_path AS p
         JOIN gar.v_objects o ON o.object_id = p.object_id AND o.types[1] NOT IN ('carplace', 'steads')
WHERE p.is_active = 1 AND p.object_id > 50000000
    --AND p.path_ltree <@ '169363.169398'::ltree
--and p.object_id = 2465884
ORDER BY p.object_id
LIMIT 1000;


explain
select p.object_id,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
from "gar"."v_adm_hierarchy_path" as "p"
         inner join "gar"."v_objects" as "o" on "o"."object_id" = "p"."object_id" --and o.types[1] NOT IN ('stead', 'carplace')
where "p"."is_active" = 1 and "p"."object_id" > 50000000
order by "p"."object_id" asc
limit 1000;

-- итерацией по path
explain analyze
SELECT p.object_id,
    p.path_ltree,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_adm_hierarchy_path AS p
         JOIN gar.v_objects o ON o.object_id = p.object_id AND o.types[1] NOT IN ('carplace', 'steads')
WHERE p.object_id > 50000000 AND p.is_active = 1
    --aND p.path_ltree <@ '169363.169398'::ltree
ORDER BY p.object_id
LIMIT 1000;

-- итерацией по path
explain analyze
SELECT o.object_id,
    p.path_ltree,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_adm_hierarchy_path AS p
         JOIN gar.v_objects o ON o.object_id = p.object_id AND o.types[1] NOT IN ('carplace', 'steads')
WHERE
    --p.object_id > 50000000
    p.is_active = 1
    --aND p.path_ltree <@ '169363.169398'::ltree
        and p.object_id = 1470768
ORDER BY o.object_id
LIMIT 10;


-- быстрый вариант, но возвращает пустые время от времени
explain analyze
SELECT o.object_id
FROM gar.v_objects AS o
         JOIN LATERAL ( SELECT p.path_ltree, is_active
                        FROM gar.v_adm_hierarchy_path p
                        WHERE p.object_id = o.object_id AND p.path_ltree <@ '169363.169398'::ltree) AS pp
ON pp.is_active = 1
WHERE o.object_id > 50000000 AND o.types[1] NOT IN ('carplace', 'steads')
ORDER BY o.object_id
LIMIT 10;

--101726152
--50000000
-- select max(object_id) from gar.v_objects; == 101726152

-- быстрый вариант, но возвращает пустые время от времени
explain analyze
SELECT t.*, p.path_ltree
FROM (
         SELECT o.object_id
         FROM gar.v_objects AS o
             -- JOIN gar.v_adm_hierarchy_path p ON p.object_id = o.object_id and p.is_active = 1
         WHERE o.object_id > 50000000 AND o.types[1] NOT IN ('carplace', 'steads')
         ORDER BY o.object_id
         LIMIT 10
     ) AS t
         JOIN gar.v_adm_hierarchy_path p ON p.object_id = t.object_id and p.is_active = 1
WHERE p.path_ltree <@ '169363.169398'::ltree;


-- супер новый с итерацией по object
explain analyze
SELECT o.object_id, p.path_ltree
FROM gar.v_objects AS o
         JOIN gar.v_adm_hierarchy_path p ON p.object_id = o.object_id AND p.is_active = 1
WHERE o.object_id > 50000000
    --AND o.types[1] NOT IN ('carplace', 'steads')
    -- AND p.path_ltree <@ '169363.169398'::ltree
ORDER BY o.object_id
LIMIT 10;

-- а здесь работает нормально если получаем только hr.id (если Index Only scan)
explain analyze
SELECT o.object_id, o.relations, hr.areacode
FROM gar.v_objects AS o
         JOIN gar.adm_hierarchy hr ON hr.objectid = o.object_id AND hr.isactive = 1
WHERE o.object_id > 50000000
    --AND o.types[1] NOT IN ('carplace', 'steads')
    -- AND p.path_ltree <@ '169363.169398'::ltree
ORDER BY o.object_id
LIMIT 10;


explain analyze
SELECT p.path
FROM gar.v_adm_hierarchy_path p
WHERE p.object_id > 5000000 and p.is_active = 1
LIMIT 10;


-- супер новый с итерацией по object
-- супер новый с итерацией по object
--explain analyze
explain analyze
SELECT o.object_id, p.path_ltree
FROM gar.v_objects AS o
         JOIN gar.v_adm_hierarchy_path p ON p.object_id = o.object_id --and p.is_active = 1
WHERE o.object_id > 50000000
    --AND o.types[1] NOT IN ('carplace', 'steads')
    --  AND p.path_ltree <@ '169363.169398'::ltree
GROUP BY o.object_id, p.path_ltree
ORDER BY o.object_id ASC
LIMIT 10;

-- супер новый с итерацией по object
-- супер новый с итерацией по object
--explain analyze
explain analyze
SELECT o.object_id,
    p.path_ltree,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_objects AS o
         JOIN gar.v_adm_hierarchy_path p ON p.object_id = o.object_id and p.is_active = 1
WHERE
    --o.object_id > 23239269 AND
    o.types[1] NOT IN ('carplace', 'steads')
-- AND o.object_id = 80354205
--AND o.object_id = 52506519
        AND o.object_id > 0
--AND p.path_ltree <@ '169363.169398'::ltree
--AND p.path_ltree <@ '5705.6143'::ltree
    --AND o.object_id = 1122258
        AND o.object_id = 52506519
ORDER BY o.object_id
LIMIT 10;

--explain analyze
SELECT *,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_objects_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_objects AS o
--FROM gar.v_adm_hierarchy_path AS p
         JOIN WHERE p.is_active = 1 -- весь путь актуален (так как история перемещений нас не интересует)
    AND p.object_id > 0 AND p.types[0] != 'carplace'
    -- AND p.path_ltree <@ '169363.169398'::ltree
--         and p.path_ltree = '5705.6143'::ltree
-- ORDER BY p.object_id
LIMIT 10;

-- WHERE object_id > 1 and types::varchar[] <> array ['stead']::varchar[] and
--     types::varchar[] <> array ['carplace']::varchar[]
-- limit 10;

-- новый вариант
--- с параметрами каждого relation
explain analyse
SELECT hr.id AS hierarchy_id,
    hr.objectid AS object_id,
    p.path_ltree,
    (-- сюда попадут и переименования
        SELECT array_to_json(array_agg(jsonb_build_object('relation', row_to_json(r2.*), 'params', (
                                                                                                       SELECT array_to_json(array_agg(row_to_json(params.*)))
                                                                                                       FROM gar.v_adm_hierarchy_actual_params AS params
                                                                                                       WHERE params.object_id = r2.object_id
                                                                                                   ))))
        FROM unnest(p.path) AS pid
                 JOIN gar.adm_hierarchy AS hr2 on hr2.objectid = pid AND hr2.isactive = 1
                 JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id
        -- доп. условий не накладываем, так как нам нужны и переименования тоже
    ) AS parents
FROM gar.adm_hierarchy AS hr
         JOIN gar.v_adm_hierarchy_path AS p ON p.hierarchy_id = hr.id
    -- нужно
        AND "p"."is_active" = 1 -- +++
-- этот join нужен чтобы отфильтровать stead и car_place, но этот join дублирует записи
         JOIN gar.v_adm_hierarchy_relation AS r
ON r.hierarchy_id = hr.id and r.relation_type not in ('stead', 'car_place')
    -- эти условия нужны чтобы не было дублирования с неактивными relation (информация о которых все равно попадет в parents.relation)
        and r.relation_is_actual = 1 and r.relation_is_active = 1
-- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND p.is_active = 1
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
    -- and p.path_ltree <@ '5705.11745.13232.15675'::ltree
    -- для проверки когда есть много relation
    --  and p.path_ltree = '5705.6326.8883.70743973'::ltree
    -- and p.path_ltree = '5705.6143.5791.11454'::ltree
    -- and p.path_ltree = '1121548.1189734.1160199.1195018'::ltree
    --and p.path_ltree = '169363.169374.170163'::ltree
    --and p.path_ltree = '692248.698593.698899'::ltree
    --and p.path_ltree = '976397.986313.986685.998330.989833'::ltree
--         and p.path_ltree = '976397.986313.986685.998330.989833.52602183'::ltree
--         and p.path_ltree = '5705.6143'::ltree
    --and p.path_ltree  <@ '169363.169398'::ltree
    -- and p.path_ltree  <@ '169363.192918.192938'::ltree
    --Failed to build address for "object_id"="182652" with message "There are no actual relations for one address level "40""
    --and p.object_id = 182652
ORDER BY hr.id
LIMIT 10;

-- новый вариант
--- с параметрами каждого relation
--explain analyse
SELECT hr.id AS hierarchy_id, hr.objectid AS object_id, p.path_ltree, p.parents
FROM gar.adm_hierarchy AS hr
         JOIN gar.v_adm_hierarchy_path AS p ON p.hierarchy_id = hr.id AND "p"."is_active" = 1 -- нужно
-- этот join нужен чтобы отфильтровать stead и car_place, но этот join дублирует записи
         JOIN gar.v_adm_hierarchy_relation AS r
ON r.hierarchy_id = hr.id and r.relation_type not in ('stead', 'car_place')
    -- эти условия нужны чтобы не было дублирования с неактивными relation (информация о которых все равно попадет в parents.relation)
        and r.relation_is_actual = 1 and r.relation_is_active = 1
-- условие чтобы убрать дублирующие неактивные пути, так как история перемещений нас не интересует
        AND p.is_active = 1
WHERE hr.isactive = 1 -- это условие уберет повторы связанные с переводом в другой level
    -- and p.path_ltree <@ '5705.11745.13232.15675'::ltree
    -- для проверки когда есть много relation
    --  and p.path_ltree = '5705.6326.8883.70743973'::ltree
    -- and p.path_ltree = '5705.6143.5791.11454'::ltree
    -- and p.path_ltree = '1121548.1189734.1160199.1195018'::ltree
    --and p.path_ltree = '169363.169374.170163'::ltree
    --and p.path_ltree = '692248.698593.698899'::ltree
    --and p.path_ltree = '976397.986313.986685.998330.989833'::ltree
--         and p.path_ltree = '976397.986313.986685.998330.989833.52602183'::ltree
--         and p.path_ltree = '5705.6143'::ltree
    --and p.path_ltree  <@ '169363.169398'::ltree
    -- and p.path_ltree  <@ '169363.192918.192938'::ltree
    --Failed to build address for "object_id"="182652" with message "There are no actual relations for one address level "40""
        and p.object_id = 182652
ORDER BY hr.id
LIMIT 10;


SELECT reltuples AS estimate
FROM pg_class
where relname = 'adm_hierarchy';



select addr_obj.name, addr_obj.objectguid, param.value
from gar.addr_obj
         join gar.param on param.typeid = 11 and param.objectid = addr_obj.objectid
where addr_obj.level = '1'
limit 10;

CREATE VIEW gar.v_adm_hierarchy_path_ext AS
SELECT p.*,
    (
        SELECT array_to_json(array_agg(jsonb_build_object('relation', row_to_json(r2.*), 'params', (
                                                                                                       SELECT array_to_json(array_agg(row_to_json(params.*)))
                                                                                                       FROM gar.v_adm_hierarchy_actual_params AS params
                                                                                                       WHERE params.object_id = r2.object_id
                                                                                                   ))))
        FROM unnest(p.path) AS pid
                 JOIN gar.adm_hierarchy AS hr2
        on hr2.objectid = pid AND hr2.isactive = 1 -- для каждого объекта из пути мы находим актуальное положение в иерархии
            -- (переходы по иерархии мы таким образом игнорируем)
                 JOIN gar.v_adm_hierarchy_relation r2 ON r2.hierarchy_id = hr2.id -- затем получаем все relations к этому положению
        -- (переименования в том числе)
    ) AS parents
FROM gar.v_adm_hierarchy_path AS p;

SELECT t.objectid AS object_id,
    array_agg(DISTINCT t.type) AS types,
    array_to_json(array_agg(jsonb_build_object('id', t.relation_id, 'is_active', t.relation_is_active, 'is_actual',
                                               t.relation_is_actual, 'type', t.type, 'data',
                                               COALESCE(t.addr_obj, t.house, t.room, t.apartment, t.carplace,
                                                        t.stead)))) AS relations
FROM (
         SELECT ho.objectid,
             row_to_json(addr_obj) AS addr_obj,
             row_to_json(houses) AS house,
             row_to_json(apartments) AS apartment,
             row_to_json(rooms) AS room,
             row_to_json(carplaces) AS carplace,
             row_to_json(steads) AS stead,
             COALESCE(addr_obj.id, houses.id, rooms.id, apartments.id, carplaces.id, steads.id) AS relation_id,
             COALESCE(addr_obj.isactive, houses.isactive, rooms.isactive, apartments.isactive, carplaces.isactive,
                      steads.isactive) AS relation_is_active,
             COALESCE(addr_obj.isactual, houses.isactual, rooms.isactual, apartments.isactual, carplaces.isactual,
                      steads.isactual) AS relation_is_actual,
             CASE WHEN addr_obj.id IS NOT NULL
                      THEN 'addr_obj'
                  WHEN houses.id IS NOT NULL
                      THEN 'house'
                  WHEN apartments.id IS NOT NULL
                      THEN 'apartment'
                  WHEN rooms.id IS NOT NULL
                      THEN 'room'
                  WHEN carplaces.id IS NOT NULL
                      THEN 'carplace'
                  WHEN steads.id IS NOT NULL
                      THEN 'stead'
             END AS type
         FROM (
                  SELECT DISTINCT hr.objectid
                  FROM gar.adm_hierarchy hr
              ) as ho
                  LEFT JOIN gar.addr_obj ON addr_obj.objectid = ho.objectid
                  LEFT JOIN gar.houses ON houses.objectid = ho.objectid
                  LEFT JOIN gar.rooms ON rooms.objectid = ho.objectid
                  LEFT JOIN gar.apartments ON apartments.objectid = ho.objectid
                  LEFT JOIN gar.carplaces ON carplaces.objectid = ho.objectid
                  LEFT JOIN gar.steads ON steads.objectid = ho.objectid
     ) AS t
WHERE t.objectid = 182652
GROUP BY t.objectid;



SELECT p.objectid AS object_id,
    array_to_json(array_agg(
            jsonb_build_object('type_id', p.typeid, 'value', p.value, 'start_date', p.startdate, 'end_date', p.enddate,
                               'is_actual', p.changeidend = 0))) AS values
FROM gar.param p
WHERE p.objectid = 182652 -- AND p.changeidend = 0
GROUP BY p.objectid;

select *
from (
         select sum(is_active) ss from gar.v_adm_hierarchy_path group by object_id
     ) t
where t.ss > 1;


SELECT pid, age(clock_timestamp(), query_start), usename, query
FROM pg_stat_activity
WHERE query != '<IDLE>' AND query NOT ILIKE '%pg_stat_activity%' AND age(clock_timestamp(), query_start) > '00:05:00'
ORDER BY query_start desc;
