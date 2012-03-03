CREATE TABLE lxc_ver(
	id							SERIAL PRIMARY KEY,
	datum						timestamp with time zone DEFAULT NOW(),
	version					varchar(30),
	subversion				varchar(30)
);
INSERT INTO lxc_ver (version) VALUES ('1.4.3-0');

CREATE TABLE lxc_a(											-- Ein Auftrag.. 
	lxc_a_id 				SERIAL PRIMARY KEY,
	lxc_a_c_id  			integer NOT NULL,  			/* zu welchem Fhz gehört der A */
	lxc_a_init_time		timestamp with time zone DEFAULT NOW(),	/* Auftragseröffung */	
	lxc_a_finish_time		varchar(50),					-- Ferrtigstellung
	lxc_a_modified_from	varchar(20),					/* Wer hat ihn angefasst */
	lxc_a_modified_on		varchar(20),					/* Wann wurde er angefasst */
	lxc_a_km					integer DEFAULT 1,			/*	Letzter KM-Stand  */
	lxc_a_status			smallint DEFAULT 1,			/* Auftragsstatus 1=>nicht angefangen, 2=>in Bearbeitung, 3=>fertig und geprüft, 4=>abgerechnet, geschlossen */
	lxc_a_text				text
);
CREATE INDEX  lxc_a_c_id_idx ON lxc_a(lxc_a_c_id);

CREATE TABLE lxc_a_pos(								/*	Die Auftragspositionenen */		
	lxc_a_pos_id			SERIAL PRIMARY KEY,	/* die ID..# */
	lxc_a_pos_aid			integer NOT NULL,		/* zu welchem Auzftrag gegört die Position */
	lxc_a_pos_todo			text,						/*	Auftragstext */
	lxc_a_pos_doing		text,						/* Was wurde getan */
	lxc_a_pos_parts		text,						/* Welche Ersatzteile wurden verbaut */
	lxc_a_pos_time			real DEFAULT 0,		/* Wieviel Zeit wurde benötigt */		
	lxc_a_pos_ctime		real DEFAULT 0,		/* Vorgegebene Zeit, Richtreit lt. Autodata, Coparts, etc */
	lxc_a_pos_emp			varchar(20),			/* Wer hats getan??? ToDo in smallint ändern */
	lxc_a_pos_status		smallint DEFAULT 1	/* Bearbeitungsstatus 1=>nicht angefangen, 2=>in Bearbeitung, 3=>fertig*/
);
CREATE INDEX	lxc_a_pos_aid_idx ON lxc_a_pos(lxc_a_pos_aid);

CREATE TABLE lxc_cars(
	c_id			SERIAL PRIMARY KEY,
	c_ow  		integer NOT NULL,							/* Kundennummer */
	c_ln  		char varying(10) NOT NULL UNIQUE,	/* Kennzeichen */
	c_2   		char varying(4), 							/* zu 2 (HerstellerNummer) */
	c_3   		char varying(10),							/* zu 3 (TypNummer) */
	c_em			char varying(6),							/* Emmisionschluessel */
	c_mkb			char varying(20),							/* Motorkennbuchstabe */
   c_t   		char varying(5),							/* CarType      	    */
  	c_d   	  	date,											/* Datum der Zulassung */
   c_hu  	  	date,											/* Datum HU */
   c_fin 	  	char varying(20) UNIQUE DEFAULT NULL,	/* FahrzeugIdentNummer */
   c_st  	  	char varying(30),							/* Sommerreifen195/70R15 91W 7Jx15H2 */
   c_wt  	  	char varying(30),							/* Winterreifen */
   c_st_l 	  	char varying(30),							/* Lagerort SR */
   c_wt_l     	char varying(30),							/* Lagerort WR */
 	c_it       	timestamp DEFAULT now(), 				/* angelegt am */	
   c_mt       	varchar(30), 								/* modifiziert am */
   c_e_id     	varchar(30), 								/* bearbeitet von */
   c_text     	text,
   c_st_z		varchar(30),								/* Zustand Sommerreifen */
   c_wt_z		varchar(30),								/* Zustand Winterreifen 	*/
   c_color		varchar(30),								/* FArbnummer           	*/
   c_gart		varchar(30),								/* Getriebeart */ 
   c_m			varchar(5));								/* Bemerkungen */
CREATE INDEX  c_ow_idx ON lxc_cars(c_ow);			   /* Index fuer Suche nach Besitzer */
CREATE INDEX  c_ln_idx ON lxc_cars(c_ln); 
CREATE INDEX  c_t_idx ON lxc_cars(c_t); 		
CREATE INDEX  c_m_idx ON lxc_cars(c_m); 			




		
		