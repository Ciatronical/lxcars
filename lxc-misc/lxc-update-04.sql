CREATE TABLE lxc_flex
(
  id serial,
  hsn varchar,
  tsn varchar,
  flxgr text,
  hubr text,
  leist text,
  baujvon text,
  baujbis text,
  PRIMARY KEY (hsn, tsn)
);
ALTER TABLE lxc_cars ADD COLUMN c_zrd text;
ALTER TABLE lxc_cars ADD COLUMN c_zrk integer;
ALTER TABLE lxc_cars ADD COLUMN c_bf text;
ALTER TABLE lxc_cars ADD COLUMN c_wd text;
INSERT INTO lxc_ver (version) VALUES ('1.4.3-4');