select * from customer;

DROP DOMAIN IF EXISTS zipcode_type CASCADE; --rm
CREATE DOMAIN zipcode_type varchar(12)
    CONSTRAINT valid_zipcode
    CHECK (VALUE ~ '[A-Z0-9-]+');
    
DROP TABLE IF EXISTS builing CASCADE;   
CREATE TABLE builing(
    builing_id INT GENERATED ALWAYS AS IDENTITY,
    PRIMARY KEY(builing_id),
    customer_id INT REFERENCES customer(id),
    description VARCHAR(255) NOT NULL,
    construction_date DATE NOT NULL DEFAULT CURRENT_DATE,
    street VARCHAR(255),
    zipcode zipcode_type,
    city VARCHAR(255)
);

DROP TABLE IF EXISTS flat CASCADE;
CREATE TABLE flat(
    flat_id INT GENERATED ALWAYS AS IDENTITY,
    PRIMARY KEY(flat_id),
    customer_id INT REFERENCES customer(id),
    building_id INT REFERENCES builing(builing_id),
    description VARCHAR(255) NOT NULL,
    livingspace NUMERIC (3, 2) DEFAULT 0.0
);
DROP TABLE IF EXISTS countertype CASCADE;
CREATE TABLE countertype(
    countertype_id INT GENERATED ALWAYS AS IDENTITY,
    PRIMARY KEY(countertype_id),
    description VARCHAR(255) NOT NULL
);
DROP TABLE IF EXISTS counter CASCADE;
CREATE TABLE counter(
    counter_id INT GENERATED ALWAYS AS IDENTITY,
    PRIMARY KEY(counter_id),
    description VARCHAR(255) NOT NULL,
    counter_number VARCHAR(255),
    flat_id INT REFERENCES flat(flat_id),
    building_id INT REFERENCES builing(builing_id),
    countertype_id INT REFERENCES countertype(countertype_id)
);

SELECT * FROM units;

DROP TABLE IF EXISTS consumption CASCADE;
CREATE TABLE meterreading(--verbrauch meterreading
    meterreading_id INT GENERATED ALWAYS AS IDENTITY,
    PRIMARY KEY(meterreading_id),
    description VARCHAR(255),
    meterreading_valie INT NOT NULL DEFAULT 1,
    meterreading_date DATE NOT NULL DEFAULT CURRENT_DATE
)