-- (make_apply_delta__objects_params)
DROP FUNCTION IF EXISTS app.apply_delta__objects_params(delta_from int);
CREATE
OR REPLACE FUNCTION app.apply_delta__objects_params(delta_from int) RETURNS VOID LANGUAGE plpgsql AS
$$
BEGIN
INSERT INTO app.objects_params (object_id, max_delta_version, agg_values)
SELECT object_id,
       max_delta_version,
       agg_values
FROM app.fly_objects_params
WHERE max_delta_version >= delta_from ON CONFLICT (object_id) DO
UPDATE SET
    max_delta_version = excluded.max_delta_version,
    agg_values = excluded.agg_values;
END
$$;

-- (make_apply_delta__objects)
DROP FUNCTION IF EXISTS app.apply_delta__objects(delta_from int);
CREATE
OR REPLACE FUNCTION app.apply_delta__objects(delta_from int) RETURNS VOID
                LANGUAGE plpgsql AS
$$
BEGIN
INSERT INTO app.objects (object_id, max_delta_version, path_ltree, agg_types, relations)
SELECT object_id,
       max_delta_version,
       path_ltree,
       agg_types,
       relations
FROM app.fly_active_objects_hierarchy
WHERE max_delta_version >= delta_from ON CONFLICT (object_id) DO
UPDATE SET
    max_delta_version = excluded.max_delta_version,
    path_ltree = excluded.path_ltree,
    agg_types = excluded.agg_types,
    relations = excluded.relations;
END
$$;


-- (make_apply_delta__indexer_queue)
DROP FUNCTION IF EXISTS app.apply_delta__indexer_queue(delta_from int);
CREATE
OR REPLACE FUNCTION app.apply_delta__indexer_queue(delta_from int) RETURNS VOID
                LANGUAGE plpgsql AS
$$
BEGIN
INSERT INTO app.indexer_queue (object_id,
                               path_ltree,
                               max_delta_version,
                               objects,
                               objects_max_delta_version,
                               params,
                               params_max_delta_version,
                               lon,
                               lat)
SELECT object_id,
       path_ltree,
       max_delta_version,
       objects,
       objects_max_delta_version,
       params,
       params_max_delta_version,
       lon,
       lat
FROM app.fly_indexer_queue
WHERE max_delta_version >= delta_from ON CONFLICT (object_id) DO
UPDATE SET
    path_ltree = excluded.path_ltree,
    max_delta_version = excluded.max_delta_version,
    objects = excluded.objects,
    objects_max_delta_version = excluded.objects_max_delta_version,
    params = excluded.params,
    params_max_delta_version = excluded.params_max_delta_version,
    lon = excluded.lon,
    lat = excluded.lat;
END
$$;
