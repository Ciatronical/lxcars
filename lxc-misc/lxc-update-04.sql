ALTER TABLE lxc_cars ADD COLUMN c_flx character varying(15);
ALTER TABLE lxc_cars ADD COLUMN c_zrd date;
ALTER TABLE lxc_cars ADD COLUMN c_zrk integer;
INSERT INTO lxc_ver (version) VALUES ('1.4.3-4');