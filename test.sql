EXPLAIN ANALYZE WITH new_data (id, label, color, cat_order) AS ( VALUES ( 3, 'Werkstatt-Plan', '#111', 0 ),( 5, 'Ersatzfahrzeuge', '', 1 ),( 2, 'Urlaub / Krankheit', '#ffffff', 2 ),( 10, 'Geburtstage', '', 3 ),( 11, 'Inter-Data', '', 4 ),( 12, 'Büro', '', 5 ) ) UPDATE event_category SET label = d.label, color = d.color, cat_order = d.cat_order FROM new_data d WHERE d.id = event_category.id;

BEGIN;
EXPLAIN ANALYZE UPDATE event_category SET  label =  'Werkstatt-Plan', color = '#111', cat_order = 0 WHERE  id = 3;
UPDATE event_category SET  label =  'Ersatzfahrzeuge', color = '', cat_order = 1 WHERE  id = 5;
UPDATE event_category SET  label =  'Urlaub / Krankheit', color = '#ffffff', cat_order = 2 WHERE  id = 2;
UPDATE event_category SET  label =  'Geburtstage', color = '', cat_order = 3 WHERE  id = 10;
UPDATE event_category SET  label =  'Inter-Data', color = '', cat_order = 4 WHERE  id = 11;
UPDATE event_category SET  label =  'Büro', color = '', cat_order = 5 WHERE  id = 12;
COMMIT;


(SELECT 'true'::BOOL AS instruction, parts.instruction, instructions.id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions, parts WHERE instructions.trans_id = '153162'AND parts.id = instructions.parts_id UNION SELECT 'false'::BOOL AS instruction,parts.instruction, orderitems.id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription FROM orderitems, parts WHERE orderitems.trans_id = '153162' AND parts.id = orderitems.parts_id ORDER BY position DESC );
--SELECT 'true'::BOOL AS instruction, parts.instruction, instructions.id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions
select rate from tax join taxkeys on taxkeys.tax_id = tax.id join taxzone_charts on taxzone_charts.income_accno_id = taxkeys.chart_id where taxzone_charts.buchungsgruppen_id = 859 and taxzone_id = 4 order by startdate DESC Limit 1;
SELECT * FROM orderitems JOIN parts ON ( parts.id = orderitems.parts_id ) JOIN taxzone_charts ON ( parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id ) WHERE orderitems.trans_id = '153162';

--SELECT * FROM taxzone_charts;
--SELECT * FROM orderitems JOIN parts ON (parts.id = orderitems.parts_id) WHERE orderitems.trans_id = '153162';