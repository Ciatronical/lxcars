DROP TABLE IF EXISTS carskba;
CREATE TABLE carskba(
    hsn TEXT,
    tsn TEXT,
    hersteller TEXT,
    marke TEXT,
    name TEXT,
    datum TEXT,
    klasse TEXT,
    aufbau TEXT,
    kraftstoff TEXT,
    leistung TEXT,
    hubraum TEXT,
    achsen TEXT,
    antrieb TEXT,
    sitze TEXT,
    masse TEXT
);
COPY carskba( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung,    hubraum, achsen, antrieb, sitze, masse )
FROM '/var/www/kivitendo-crm/lxcars/kba/carskba.csv' DELIMITER '|' CSV;


DROP TABLE IF EXISTS ksart;
CREATE TABLE ksart(
    kstext TEXT,
    kscode TEXT
);
COPY ksart( kstext, kscode ) FROM '/var/www/kivitendo-crm/lxcars/kba/ksart.csv' DELIMITER '|' CSV;

SELECT * FROM carskba LEFT JOIN ksart ON ( kraftstoff::INT = kscode::INT ) WHERE hsn ILIKE '0600%' AND tsn ILIKE '%300%';

SELECT DISTINCT TRIM(klasse) FROM carskba;
SELECT * FROM carskba WHERE klasse ILIKE '%11%';