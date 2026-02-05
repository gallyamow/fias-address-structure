--
-- Связанные с каждым объектом параметры.
--
-- explain при inlined table (limit 10) даже при наличии индекса param_objectid_typeid_idx:
-- (cost=4019.54..4020.59 rows=10 width=84) (actual time=1.142..1.165 rows=10 loops=1)
--
-- explain используя app.objects_params (limit 10):
-- (cost=34.77..35.82 rows=10 width=84) (actual time=0.208..0.219 rows=10 loops=1)
-- (из make_fly_objects_params)
--
DROP VIEW IF EXISTS app.fly_objects_params;
CREATE VIEW app.fly_objects_params AS
SELECT
    p.objectid AS object_id,
    MAX(p.delta_version) AS max_delta_version,
    ARRAY_TO_JSON(
        ARRAY_AGG(
            JSONB_BUILD_OBJECT(
                'type_id', p.typeid,
                'value', p.value,
                'start_date', p.startdate,
                'end_date', p.enddate,
                'is_actual', p.changeidend = 0
            )
        )
    ) AS agg_values
FROM gar.param p
GROUP BY p.objectid;

--
-- Список всех объектов с активным path.
-- Каждый объект представлен 1 раз, содержит все свои relations.
--
-- (пробовал делать inlined- запрос без view, внеся фильтрацию delta во внутренний запрос - стоимость запроса не изменилась)
-- (из make_fly_active_objects_hierarchy)
DROP VIEW IF EXISTS app.fly_active_objects_hierarchy;
CREATE VIEW app.fly_active_objects_hierarchy AS
SELECT
    t.object_id AS object_id,
    t.path::ltree  AS path_ltree,
    MAX(t.max_delta_version) AS max_delta_version,
    ARRAY_AGG(DISTINCT t.type) AS agg_types,
    ARRAY_TO_JSON(
        ARRAY_AGG(
            JSONB_BUILD_OBJECT(
                'id', t.relation_id,
                'is_active', t.relation_is_active,
                'is_actual', t.relation_is_actual,
                'type', t.type,
                'data', COALESCE(t.addr_obj, t.house, t.room, t.apartment, t.carplace, t.stead)
            )
        )
    ) AS relations
FROM (SELECT
          aho.objectid  AS object_id,
          aho.path AS path,
          GREATEST(
              addr_obj.delta_version,
              houses.delta_version,
              apartments.delta_version,
              rooms.delta_version,
              carplaces.delta_version,
              steads.delta_version
          ) AS max_delta_version,
          ROW_TO_JSON(addr_obj)                                                              AS addr_obj,
          ROW_TO_JSON(houses)                                                                AS house,
          ROW_TO_JSON(apartments)                                                            AS apartment,
          ROW_TO_JSON(rooms)                                                                 AS room,
          ROW_TO_JSON(carplaces)                                                             AS carplace,
          ROW_TO_JSON(steads)                                                                AS stead,
          COALESCE(addr_obj.id, houses.id, rooms.id, apartments.id, carplaces.id, steads.id) AS relation_id,
          COALESCE(addr_obj.isactive, houses.isactive, rooms.isactive, apartments.isactive, carplaces.isactive,
                   steads.isactive)                                                          AS relation_is_active,
          COALESCE(addr_obj.isactual, houses.isactual, rooms.isactual, apartments.isactual, carplaces.isactual,
                   steads.isactual)                                                          AS relation_is_actual,
          CASE WHEN addr_obj.id IS NOT NULL THEN 'addr_obj'
               WHEN houses.id IS NOT NULL THEN 'house'
               WHEN apartments.id IS NOT NULL THEN 'apartment'
               WHEN rooms.id IS NOT NULL THEN 'room'
               WHEN carplaces.id IS NOT NULL THEN 'carplace'
               WHEN steads.id IS NOT NULL THEN 'stead'
              END AS type
      FROM (
               -- на каждый объект должна быть только 1 запись
               SELECT t4.objectid, t4.path, t4.isactive
               FROM gar.adm_hierarchy AS t4
               WHERE t4.isactive = 1
           ) AS aho
               LEFT JOIN gar.addr_obj ON addr_obj.objectid = aho.objectid
               LEFT JOIN gar.houses ON houses.objectid = aho.objectid
               LEFT JOIN gar.rooms ON rooms.objectid = aho.objectid
               LEFT JOIN gar.apartments ON apartments.objectid = aho.objectid
               LEFT JOIN gar.carplaces ON carplaces.objectid = aho.objectid
               LEFT JOIN gar.steads ON steads.objectid = aho.objectid
     ) AS t
GROUP BY t.object_id, t.path;

--
-- Готовые к индексации данные.
-- 'carplace', 'stead' - исключили из индексирования, хотя заполняем таблицы.
-- (из make_fly_indexer_queue)
--
DROP VIEW IF EXISTS app.fly_indexer_queue;
CREATE VIEW app.fly_indexer_queue AS
SELECT
    o.object_id,
    o.path_ltree,
    o.max_delta_version,
    nested_objects.objects           AS objects,
    nested_objects.max_delta_version AS objects_max_delta_version,
    nested_params.params             AS params,
    nested_params.max_delta_version  AS params_max_delta_version,
    geo_data.lon AS lon,
    geo_data.lat AS lat
FROM app.objects AS o
         LEFT JOIN app.objects_geo_data AS geo_data ON geo_data.object_id = o.object_id
         JOIN LATERAL (
    SELECT
        ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(no.*))) AS objects,
        MAX(no.max_delta_version)                   AS max_delta_version
    FROM app.objects AS no
    WHERE no.object_id = ANY (STRING_TO_ARRAY(o.path_ltree::VARCHAR, '.')::bigint[])
) AS nested_objects ON TRUE
    JOIN LATERAL (
    SELECT
    ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(pm.*))) AS params,
    MAX(pm.max_delta_version)                   AS max_delta_version
    FROM app.objects_params AS pm
    WHERE pm.object_id = ANY (STRING_TO_ARRAY(o.path_ltree::VARCHAR, '.')::BIGINT[])
) AS nested_params ON TRUE
    WHERE o.agg_types[1] NOT IN ('carplace', 'stead');

