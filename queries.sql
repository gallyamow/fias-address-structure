-- select
SELECT
    o.object_id,
    p.path_ltree,
    (
        SELECT array_to_json(array_agg(row_to_json(o.*))) FROM gar.v_objects AS o WHERE o.object_id = ANY (p.path)
    ) AS objects,
    (
        SELECT array_to_json(array_agg(row_to_json(pm.*)))
        FROM gar.v_object_params AS pm
        WHERE pm.object_id = ANY (p.path)
    ) AS params
FROM gar.v_objects AS o
         JOIN gar.v_adm_hierarchy_path p ON p.object_id = o.object_id and p.is_active = 1
WHERE o.object_id > 0 AND o.types[1] NOT IN ('carplace', 'steads')
        AND o.object_id = 80354205
ORDER BY o.object_id
LIMIT 10;



-- tree [
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
CREATE INDEX ON gar.v_adm_hierarchy_path(object_id);
CREATE INDEX ON gar.v_adm_hierarchy_path USING GIST(path_ltree);
-- не потребовался?
-- ]

-- Список всех объектов. Каждый объект представлен 1 раз, содержит все свои relations
-- v_objects [
DROP MATERIALIZED VIEW IF EXISTS gar.v_objects;
CREATE MATERIALIZED VIEW gar.v_objects AS
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
GROUP BY t.objectid;
CREATE UNIQUE INDEX ON gar.v_objects(object_id);
-- колонка types всегда содержит только 1 значение
CREATE INDEX ON gar.v_objects(object_id, (types[1]));
-- GIN index for types or expression index ?
-- ] v_objects

-- Связанные с каждым объектом параметры
DROP MATERIALIZED VIEW IF EXISTS gar.v_object_params;
CREATE MATERIALIZED VIEW gar.v_object_params AS
SELECT p.objectid AS object_id,
    array_to_json(array_agg(
            jsonb_build_object('type_id', p.typeid, 'value', p.value, 'start_date', p.startdate, 'end_date', p.enddate,
                               'is_actual', p.changeidend = 0))) AS values
FROM gar.param p
GROUP BY p.objectid;
CREATE UNIQUE INDEX ON gar.v_object_params(object_id);
-- ] v_object_params
