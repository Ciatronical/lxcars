-- @tag: LxcarsAuftrag
-- @description: Add columns to tables
-- @version: 2.3.1

ALTER TABLE oe ADD km_stnd INT DEFAULT 0;
ALTER TABLE oe ADD c_id INT;
ALTER TABLE oe ADD status TEXT;
ALTER TABLE oe ADD car_status TEXT;
ALTER TABLE oe ADD finish_time TEXT;

CREATE  TABLE instructions(
    id serial,
    trans_id integer,
    parts_id integer,
    description text,
    response text,
    longdescription text,
    sellprice numeric(15,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    ordnumber text,
    "position" integer NOT NULL,
    status text,
    u_id text,
    employee text,
    dictated_during tsrange,
    needed_during tsrange
);

--ALTER TABLE orderitems ADD status TEXT;
--ALTER TABLE orderitems ADD u_id TEXT;

--ALTER TABLE oe ALTER COLUMN km_stnd SET DEFAULT 0;
--ALTER TABLE oe ADD finish_time TEXT;

-- @exec
