-- @tag: LxcarsAuftrag
-- @description: Add columns to tables
-- @version: 2.3.1

ALTER TABLE oe ADD km_stnd INT DEFAULT 0;
ALTER TABLE oe ADD c_id INT;
ALTER TABLE oe ADD status TEXT;
ALTER TABLE oe ADD car_status TEXT;
ALTER TABLE oe ADD finish_time TEXT;

ALTER TABLE orderitems ADD status TEXT;
ALTER TABLE orderitems ADD u_id TEXT;

--ALTER TABLE oe ALTER ColUMN km_stnd SET DEFAULT 0;

-- @exec
