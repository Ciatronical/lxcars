-- @tag: LxcarsAuftrag
-- @description: Add columns to tables
-- @version: 2.3.1

ALTER TABLE oe ADD km_stnd INT;
ALTER TABLE oe ADD c_id INT;
ALTER TABLE oe ADD status TEXT;
ALTER TABLE oe ADD car_status TEXT;

ALTER TABLE orderitems ADD status TEXT;
ALTER TABLE orderitems ADD u_id TEXT;

-- @exec
