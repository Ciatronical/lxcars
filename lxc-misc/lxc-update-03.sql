ALTER TABLE lxc_mykba ADD COLUMN emp VARCHAR(32);
ALTER TABLE lxc_mykba ADD COLUMN zylinder VARCHAR(4);
ALTER TABLE lxc_mykba ADD COLUMN ventile VARCHAR(4);
ALTER TABLE lxc_mykba ADD COLUMN radstand VARCHAR(8);
ALTER TABLE lxc_mykba ADD COLUMN bearbeitet_am VARCHAR(24);
ALTER TABLE lxc_mykba ADD PRIMARY KEY (tsn, hsn);
INSERT INTO lxc_ver (version) VALUES ('1.4.3-3');