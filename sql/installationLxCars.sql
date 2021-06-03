--Installation LxCars

/******* Begin lxc_cars ********************/

CREATE TABLE public.lxc_cars
(
    c_id SERIAL,
    c_ow integer NOT NULL,
    c_ln character varying(10) COLLATE pg_catalog."default" NOT NULL,
    c_2 character varying(4) COLLATE pg_catalog."default",
    c_3 character varying(10) COLLATE pg_catalog."default",
    c_em character varying(6) COLLATE pg_catalog."default",
    c_mkb character varying(20) COLLATE pg_catalog."default",
    c_t character varying(5) COLLATE pg_catalog."default",
    c_d date,
    c_hu date,
    c_fin character varying(30) COLLATE pg_catalog."default" DEFAULT NULL::character varying,
    c_st character varying(30) COLLATE pg_catalog."default",
    c_wt character varying(30) COLLATE pg_catalog."default",
    c_st_l character varying(30) COLLATE pg_catalog."default",
    c_wt_l character varying(30) COLLATE pg_catalog."default",
    c_it timestamp without time zone DEFAULT now(),
    c_mt character varying(30) COLLATE pg_catalog."default",
    c_e_id character varying(30) COLLATE pg_catalog."default",
    c_text text COLLATE pg_catalog."default",
    c_m character varying(5) COLLATE pg_catalog."default",
    c_color character varying(30) COLLATE pg_catalog."default",
    c_gart character varying(30) COLLATE pg_catalog."default",
    c_st_z character varying(30) COLLATE pg_catalog."default",
    c_wt_z character varying(30) COLLATE pg_catalog."default",
    chk_c_ln boolean DEFAULT true,
    chk_c_2 boolean DEFAULT true,
    chk_c_3 boolean DEFAULT true,
    chk_c_em boolean DEFAULT true,
    chk_fin boolean DEFAULT true,
    chk_c_hu boolean DEFAULT true,
    c_zrk integer,
    c_zrd text COLLATE pg_catalog."default",
    c_bf text COLLATE pg_catalog."default",
    c_wd text COLLATE pg_catalog."default",
    CONSTRAINT cars6_pkey PRIMARY KEY (c_id),
    CONSTRAINT cars6_c_ln_key UNIQUE (c_ln),
    CONSTRAINT lxc_cars_c_fin_key UNIQUE (c_fin)
);

CREATE INDEX c_ln_idx
    ON public.lxc_cars USING btree
    (c_ln COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: c_m_idx


CREATE INDEX c_m_idx
    ON public.lxc_cars USING btree
    (c_m COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: c_ow_idx

CREATE INDEX c_ow_idx
    ON public.lxc_cars USING btree
    (c_ow ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: c_t_idx

CREATE INDEX c_t_idx
    ON public.lxc_cars USING btree
    (c_t COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;

/******* Begin lxc_flex ********************/

CREATE TABLE public.lxc_flex
(
    id SERIAL,
    hsn character varying COLLATE pg_catalog."default" NOT NULL,
    tsn character varying COLLATE pg_catalog."default" NOT NULL,
    flxgr text COLLATE pg_catalog."default",
    hubr text COLLATE pg_catalog."default",
    leist text COLLATE pg_catalog."default",
    baujvon text COLLATE pg_catalog."default",
    baujbis text COLLATE pg_catalog."default",
    CONSTRAINT lxc_flex_pkey PRIMARY KEY (hsn, tsn)
);

/******* Begin lxc_mykba ********************/

CREATE TABLE public.lxc_mykba
(
    id SERIAL,
    my_typ_nr character(5) COLLATE pg_catalog."default",
    hsn character(4) COLLATE pg_catalog."default" NOT NULL,
    tsn character varying(16) COLLATE pg_catalog."default" NOT NULL,
    marke character varying(32) COLLATE pg_catalog."default",
    typ character varying(32) COLLATE pg_catalog."default",
    bezeichung character varying(32) COLLATE pg_catalog."default",
    hersteller character varying(32) COLLATE pg_catalog."default",
    klasse_aufbau character varying(32) COLLATE pg_catalog."default",
    eg_typ_schadst character varying(32) COLLATE pg_catalog."default",
    na_schad_klasse character varying(32) COLLATE pg_catalog."default",
    kraftstoff character varying(32) COLLATE pg_catalog."default",
    kraftstoff_code character varying(8) COLLATE pg_catalog."default",
    eg_schad_code character varying(8) COLLATE pg_catalog."default",
    hubraum_ccm character varying(8) COLLATE pg_catalog."default",
    bemerkungen text COLLATE pg_catalog."default",
    anzahl_achsen character varying(4) COLLATE pg_catalog."default",
    anzahl_ant_achs character varying(4) COLLATE pg_catalog."default",
    leistung_drehz character varying(16) COLLATE pg_catalog."default",
    geschwindigkeit character varying(4) COLLATE pg_catalog."default",
    laenge character varying(16) COLLATE pg_catalog."default",
    breite character varying(16) COLLATE pg_catalog."default",
    hoehe character varying(16) COLLATE pg_catalog."default",
    masse_leer character varying(16) COLLATE pg_catalog."default",
    tankinhalt character varying(8) COLLATE pg_catalog."default",
    stuetzlast character varying(8) COLLATE pg_catalog."default",
    leitungsgewicht character varying(8) COLLATE pg_catalog."default",
    co2 character varying(8) COLLATE pg_catalog."default",
    masse_gesamt character varying(8) COLLATE pg_catalog."default",
    na_masse_gesamt character varying(8) COLLATE pg_catalog."default",
    achslast_1 character varying(8) COLLATE pg_catalog."default",
    achslast_2 character varying(8) COLLATE pg_catalog."default",
    achslast_3 character varying(8) COLLATE pg_catalog."default",
    na_achslast_1 character varying(8) COLLATE pg_catalog."default",
    na_achslast_2 character varying(8) COLLATE pg_catalog."default",
    na_achslast_3 character varying(8) COLLATE pg_catalog."default",
    standgeraeusch character varying(8) COLLATE pg_catalog."default",
    drehzahl_st_ge character varying(8) COLLATE pg_catalog."default",
    fahrgeraeusch character varying(8) COLLATE pg_catalog."default",
    anhlast_gebr character varying(8) COLLATE pg_catalog."default",
    anhlast_ungebr character varying(8) COLLATE pg_catalog."default",
    sitzplaetze character varying(4) COLLATE pg_catalog."default",
    stehplaetze character varying(4) COLLATE pg_catalog."default",
    bereifung_achs1 character varying(32) COLLATE pg_catalog."default",
    bereifung_achs2 character varying(32) COLLATE pg_catalog."default",
    bereifung_achs3 character varying(32) COLLATE pg_catalog."default",
    farbe character varying(16) COLLATE pg_catalog."default",
    farbcode character varying(8) COLLATE pg_catalog."default",
    eg_typ_abe character varying(32) COLLATE pg_catalog."default",
    dat_eg_typ_abe character varying(16) COLLATE pg_catalog."default",
    merkmal_typ_abe character varying(4) COLLATE pg_catalog."default",
    "nr_zuĺass_teil2" character varying(16) COLLATE pg_catalog."default",
    sonst_vermerke character varying(32) COLLATE pg_catalog."default",
    emp character varying(32) COLLATE pg_catalog."default",
    zylinder character varying(4) COLLATE pg_catalog."default",
    ventile character varying(4) COLLATE pg_catalog."default",
    radstand character varying(8) COLLATE pg_catalog."default",
    bearbeitet_am character varying(24) COLLATE pg_catalog."default",
    CONSTRAINT lxc_mykba_pkey PRIMARY KEY (tsn, hsn)
);

/******* Begin lxc_ver ********************/

CREATE TABLE public.lxc_ver
(
    id SERIAL,
    datum timestamp with time zone DEFAULT now(),
    version character varying(30) COLLATE pg_catalog."default",
    subversion character varying(30) COLLATE pg_catalog."default",
    CONSTRAINT lxc_ver_pkey PRIMARY KEY (id)
);

/******* Begin instructions ********************/

CREATE TABLE public.instructions
(
    trans_id integer,
    parts_id integer,
    description text COLLATE pg_catalog."default",
    qty real,
    sellprice numeric(15,5),
    discount real,
    project_id integer,
    reqdate date,
    ship real,
    serialnumber text COLLATE pg_catalog."default",
    id SERIAL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    pricegroup_id integer,
    ordnumber text COLLATE pg_catalog."default",
    transdate text COLLATE pg_catalog."default",
    cusordnumber text COLLATE pg_catalog."default",
    unit character varying(20) COLLATE pg_catalog."default",
    base_qty real,
    subtotal boolean DEFAULT false,
    longdescription text COLLATE pg_catalog."default",
    marge_total numeric(15,5),
    marge_percent numeric(15,5),
    lastcost numeric(15,5),
    price_factor_id integer,
    price_factor numeric(15,5) DEFAULT 1,
    marge_price_factor numeric(15,5) DEFAULT 1,
    "position" integer NOT NULL,
    active_price_source text COLLATE pg_catalog."default" NOT NULL DEFAULT ''::text,
    active_discount_source text COLLATE pg_catalog."default" NOT NULL DEFAULT ''::text,
    status text COLLATE pg_catalog."default",
    u_id text COLLATE pg_catalog."default",
    CONSTRAINT instructions_pkey PRIMARY KEY (id)
);

CREATE INDEX instructions_description_idx
    ON public.instructions USING gin
    (description COLLATE pg_catalog."default" gin_trgm_ops)
    TABLESPACE pg_default;
-- Index: instructions_trans_id_idx

CREATE INDEX instructions_trans_id_idx
    ON public.instructions USING btree
    (trans_id ASC NULLS LAST)
    TABLESPACE pg_default;

ALTER TABLE oe
	ADD COLUMN km_stnd integer DEFAULT 0,
	ADD COLUMN c_id integer,
	ADD COLUMN status text,
	ADD COLUMN car_status text,
	ADD COLUMN finish_time text,
	ADD COLUMN printed boolean DEFAULT false,
	ADD COLUMN car_manuf text,
	ADD COLUMN car_type text,
	ADD COLUMN internalorder boolean DEFAULT false NOT NULL;

ALTER TABLE oe
	ADD CONSTRAINT oe_currency_id_fkey1 FOREIGN KEY (currency_id) REFERENCES public.currencies(id);

ALTER TABLE parts
	ADD COLUMN instruction boolean DEFAULT false;

ALTER TABLE orderitems
	ADD COLUMN status text,
	ADD COLUMN u_id text;
CREATE INDEX idx_orderitems ON orderitems USING btree (parts_id);