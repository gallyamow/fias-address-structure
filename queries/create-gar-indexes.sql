-- не уникальный, так как - некоторые objectid дублируются (например 24317330)
CREATE INDEX ON gar.adm_hierarchy (objectid);

-- не используем эту иерархию
-- CREATE INDEX ON gar.mun_hierarchy (objectid);

CREATE INDEX ON gar.addr_obj (objectid);
CREATE INDEX ON gar.houses (objectid);
CREATE INDEX ON gar.apartments (objectid);
CREATE INDEX ON gar.carplaces (objectid);
CREATE INDEX ON gar.rooms (objectid);
CREATE INDEX ON gar.steads (objectid);
CREATE INDEX ON gar.param (objectid, typeid);

-- max_delta optimization (не используется)
-- CREATE INDEX ON gar.addr_obj (delta_version);
-- CREATE INDEX ON gar.houses (delta_version);
-- CREATE INDEX ON gar.apartments (delta_version);
-- CREATE INDEX ON gar.rooms (delta_version);
-- CREATE INDEX ON gar.carplaces (delta_version);
-- CREATE INDEX ON gar.steads (delta_version);
