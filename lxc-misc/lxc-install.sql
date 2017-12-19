
@tag: lxcars
@description: creates table for lxcars
@version: 2.3.3



CREATE TABLE IF NOT EXISTS lxc_ver(
    id                            SERIAL PRIMARY KEY,
    datum                        timestamp with time zone DEFAULT NOW(),
    version                    varchar(30),
    subversion                varchar(30)
);


CREATE TABLE  IF NOT EXISTS lxc_a(                                            -- Ein Auftrag..
    lxc_a_id                 SERIAL PRIMARY KEY,
    lxc_a_c_id              integer NOT NULL,              /* zu welchem Fhz gehört der A */
    lxc_a_init_time        timestamp with time zone DEFAULT NOW(),    /* Auftragseröffung */
    lxc_a_finish_time        varchar(50),                    -- Ferrtigstellung
    lxc_a_modified_from    varchar(20),                    /* Wer hat ihn angefasst */
    lxc_a_modified_on        varchar(20),                    /* Wann wurde er angefasst */
    lxc_a_km                    integer DEFAULT 1,            /*    Letzter KM-Stand  */
    lxc_a_status            smallint DEFAULT 1,            /* Auftragsstatus 1=>nicht angefangen, 2=>in Bearbeitung, 3=>fertig und geprüft, 4=>abgerechnet, geschlossen */
    lxc_a_text                text
);
CREATE INDEX  IF NOT EXISTS lxc_a_c_id_idx ON lxc_a(lxc_a_c_id);

CREATE TABLE IF NOT EXISTS lxc_a_pos(                                /*    Die Auftragspositionenen */
    lxc_a_pos_id            SERIAL PRIMARY KEY,    /* die ID..# */
    lxc_a_pos_aid            integer NOT NULL,        /* zu welchem Auzftrag gegört die Position */
    lxc_a_pos_todo            text,                        /*    Auftragstext */
    lxc_a_pos_doing        text,                        /* Was wurde getan */
    lxc_a_pos_parts        text,                        /* Welche Ersatzteile wurden verbaut */
    lxc_a_pos_time            real DEFAULT 0,        /* Wieviel Zeit wurde benötigt */
    lxc_a_pos_ctime        real DEFAULT 0,        /* Vorgegebene Zeit, Richtreit lt. Autodata, Coparts, etc */
    lxc_a_pos_emp            varchar(20),            /* Wer hats getan??? ToDo in smallint ändern */
    lxc_a_pos_status        smallint DEFAULT 1    /* Bearbeitungsstatus 1=>nicht angefangen, 2=>in Bearbeitung, 3=>fertig*/
);
CREATE INDEX  IF NOT EXISTS  lxc_a_pos_aid_idx ON lxc_a_pos(lxc_a_pos_aid);

CREATE TABLE IF NOT EXISTS lxc_cars(
    c_id            SERIAL PRIMARY KEY,
    c_ow          integer NOT NULL,                            /* Kundennummer */
    c_ln          char varying(10) NOT NULL UNIQUE,    /* Kennzeichen */
    c_2           char varying(4),                             /* zu 2 (HerstellerNummer) */
    c_3           char varying(10),                            /* zu 3 (TypNummer) */
    c_em            char varying(6),                            /* Emmisionschluessel */
    c_mkb            char varying(20),                            /* Motorkennbuchstabe */
   c_t           char varying(5),                            /* CarType              */
      c_d             date,                                            /* Datum der Zulassung */
   c_hu            date,                                            /* Datum HU */
   c_fin           char varying(20) UNIQUE DEFAULT NULL,    /* FahrzeugIdentNummer */
   c_st            char varying(30),                            /* Sommerreifen195/70R15 91W 7Jx15H2 */
   c_wt            char varying(30),                            /* Winterreifen */
   c_st_l           char varying(30),                            /* Lagerort SR */
   c_wt_l         char varying(30),                            /* Lagerort WR */
     c_it           timestamp DEFAULT now(),                 /* angelegt am */
   c_mt           varchar(30),                                 /* modifiziert am */
   c_e_id         varchar(30),                                 /* bearbeitet von */
   c_text         text,
   c_st_z        varchar(30),                                /* Zustand Sommerreifen */
   c_wt_z        varchar(30),                                /* Zustand Winterreifen     */
   c_color        varchar(30),                                /* FArbnummer               */
   c_gart        varchar(30),                                /* Getriebeart */
   c_m            varchar(5),                                /* Bemerkungen */
   chk_c_ln       BOOLEAN DEFAULT TRUE,
   chk_c_2        BOOLEAN DEFAULT TRUE,
   chk_c_3        BOOLEAN DEFAULT TRUE,
   chk_c_em       BOOLEAN DEFAULT TRUE,
   chk_c_hu       BOOLEAN DEFAULT TRUE,
   chk_fin        BOOLEAN DEFAULT TRUE,
   c_zrd          text,
   c_zrk          integer,
   c_bf           text,
   c_wd           text,
   c_manuf        text,
   c_type         text
 );
CREATE INDEX  IF NOT EXISTS c_ow_idx ON lxc_cars(c_ow);               /* Index fuer Suche nach Besitzer */
CREATE INDEX  IF NOT EXISTS c_ln_idx ON lxc_cars(c_ln);
CREATE INDEX  IF NOT EXISTS c_t_idx ON lxc_cars(c_t);
CREATE INDEX  IF NOT EXISTS c_m_idx ON lxc_cars(c_m);



CREATE TABLE IF NOT EXISTS lxc_mykba(
    id serial,
    my_typ_nr    char(5),
    hsn         char(4),
    tsn         varchar(16),
    marke         varchar(32),
    typ         varchar(32),
    bezeichung     varchar(32),
    hersteller    varchar(32),
    klasse_aufbau     varchar(32),
    eg_typ_schadst    varchar(32),
    na_schad_klasse    varchar(32),
    kraftstoff    varchar(32),
    kraftstoff_code    varchar(8),
    eg_schad_code    varchar(8),
    hubraum_ccm    varchar(8),
    bemerkungen    text,
    anzahl_achsen    varchar(4),
    anzahl_ant_achs    varchar(4),
    leistung_drehz    varchar(16),
    geschwindigkeit    varchar(4),
    laenge        varchar(16),
    breite        varchar(16),
    hoehe        varchar(16),
    masse_leer    varchar(16),
    tankinhalt    varchar(8),
    stuetzlast    varchar(8),
    leitungsgewicht    varchar(8),
    co2        varchar(8),
    masse_gesamt    varchar(8),
    na_masse_gesamt    varchar(8),
    achslast_1    varchar(8),
    achslast_2    varchar(8),
    achslast_3    varchar(8),
    na_achslast_1    varchar(8),
    na_achslast_2    varchar(8),
    na_achslast_3    varchar(8),
    standgeraeusch    varchar(8),
    drehzahl_st_ge    varchar(8),
    fahrgeraeusch    varchar(8),
    anhlast_gebr    varchar(8),
    anhlast_ungebr    varchar(8),
    sitzplaetze    varchar(4),
    stehplaetze    varchar(4),
    bereifung_achs1    varchar(32),
    bereifung_achs2    varchar(32),
    bereifung_achs3    varchar(32),
    farbe        varchar(16),
    farbcode    varchar(8),
    eg_typ_abe    varchar(32),
    dat_eg_typ_abe    varchar(16),
    merkmal_typ_abe    varchar(4),
    nr_zuĺass_teil2    varchar(16),
    sonst_vermerke    varchar(32),
    emp VARCHAR(32),
  zylinder VARCHAR(4),
  ventile VARCHAR(4),
  radstand VARCHAR(8),
  bearbeitet_am VARCHAR(24),
  PRIMARY KEY (tsn, hsn)

);


CREATE TABLE IF NOT EXISTS lxc_flex
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





@exec
