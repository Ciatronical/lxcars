ALTER TABLE lxc_cars ADD c_finchk CHAR(1);
UPDATE lxc_cars SET  c_fin = left( c_fin, 17 ), c_finchk  = substring( c_fin, 18, 1 );
