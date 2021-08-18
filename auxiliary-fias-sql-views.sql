-- В некоторые запросы введено использование таблицы gar.adm_hierarchy и использование его поля hierarchy_id.
-- Это сделано потому что:
--  здесь нет единой таблицы объектов.
--  здесь таблица gar.adm_hierarchy используется как связующее звено всех видов объектов из всех таблиц
--  (addr_obj, apartments, houses, carplaces, rooms, steads)

-- tree
DROP MATERIALIZED VIEW IF EXISTS gar.v_adm_hierarchy_path;
CREATE MATERIALIZED VIEW gar.v_adm_hierarchy_path AS
WITH RECURSIVE nodes(hierarchy_id, object_id, path, is_active) AS (
                                                                      SELECT hr.id AS hierarchy_id,
                                                                          hr.objectid AS object_id,
                                                                          ARRAY [hr.objectid] AS path,
                                                                          hr.isactive AS is_active
                                                                      FROM gar.adm_hierarchy hr
                                                                      WHERE hr.parentobjid = 0
                                                                      UNION ALL
                                                                      SELECT hr2.id AS hierarchy_id,
                                                                          hr2.objectid AS object_id,
                                                                          nodes.path || hr2.objectid AS path,
                                                                          -- признак активности всего пути
                                                                          nodes.is_active & hr2.isactive AS is_active
                                                                      FROM nodes, gar.adm_hierarchy hr2
                                                                      WHERE nodes.object_id = hr2.parentobjid
                                                                  )
SELECT *, array_to_string(path, '.')::ltree AS path_ltree
FROM nodes;

-- Неуникальные так как может быть ситуация когда было несколько путей, через один и тот же объект.
-- Часть путей может быть уже не активна. Объекты могут перемещаться по иерархии.
-- См. object_id=6092
CREATE INDEX ON gar.v_adm_hierarchy_path(hierarchy_id);
CREATE INDEX ON gar.v_adm_hierarchy_path(object_id);

CREATE INDEX ON gar.v_adm_hierarchy_path USING GIST(path_ltree);
-- не потребовался?

-- связанные с каждым элементом иерархии (через объект) данные (hierarchy_id)
DROP MATERIALIZED VIEW IF EXISTS gar.v_adm_hierarchy_relation;
CREATE MATERIALIZED VIEW gar.v_adm_hierarchy_relation AS
SELECT t.id AS hierarchy_id,
    t.objectid AS object_id,
    t.relation_id,
    t.relation_is_active,
    t.relation_is_actual,
    CASE WHEN t.addr_obj IS NOT NULL
             THEN 'addr_obj'
         WHEN t.house IS NOT NULL
             THEN 'house'
         WHEN t.apartment IS NOT NULL
             THEN 'apartment'
         WHEN t.room IS NOT NULL
             THEN 'room'
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
             row_to_json(apartments) AS apartment,
             row_to_json(rooms) AS room,
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

CREATE INDEX ON gar.v_adm_hierarchy_relation(hierarchy_id); -- неуникальный, потому что могут быть переименования домов и тд.  (@see objectid=70743973)
CREATE INDEX ON gar.v_adm_hierarchy_relation(object_id);

-- связанные с каждым элементом иерархии параметры (hierarchy_id)
DROP MATERIALIZED VIEW IF EXISTS gar.v_adm_hierarchy_actual_params;
CREATE MATERIALIZED VIEW gar.v_adm_hierarchy_actual_params AS
SELECT hr.id AS hierarchy_id,
    hr.objectid AS object_id,
    array_to_json(array_agg(
            jsonb_build_object('type_id', p.typeid, 'value', p.value, 'start_date', p.startdate, 'end_date',
                               p.enddate))) AS values
FROM gar.adm_hierarchy hr
         JOIN gar.param AS p ON p.objectid = hr.objectid AND p.changeidend = 0
GROUP BY hr.id;
CREATE INDEX ON gar.v_adm_hierarchy_actual_params(object_id);
-- TODO: индексы по object_id вроде больше не нужны

-- denormalized gar.v_adm_hierarchy_path
DROP MATERIALIZED VIEW IF EXISTS gar.v_adm_hierarchy_path_ext;
CREATE MATERIALIZED VIEW gar.v_adm_hierarchy_path_ext AS
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
CREATE INDEX ON gar.v_adm_hierarchy_path_ext(hierarchy_id);