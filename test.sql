EXPLAIN ANALYZE WITH new_data (id, label, color, cat_order) AS ( VALUES ( 3, 'Werkstatt-Plan', '#111', 0 ),( 5, 'Ersatzfahrzeuge', '', 1 ),( 2, 'Urlaub / Krankheit', '#ffffff', 2 ),( 10, 'Geburtstage', '', 3 ),( 11, 'Inter-Data', '', 4 ),( 12, 'Büro', '', 5 ) ) UPDATE event_category SET label = d.label, color = d.color, cat_order = d.cat_order FROM new_data d WHERE d.id = event_category.id;

BEGIN;
EXPLAIN ANALYZE UPDATE event_category SET  label =  'Werkstatt-Plan', color = '#111', cat_order = 0 WHERE  id = 3;
UPDATE event_category SET  label =  'Ersatzfahrzeuge', color = '', cat_order = 1 WHERE  id = 5;
UPDATE event_category SET  label =  'Urlaub / Krankheit', color = '#ffffff', cat_order = 2 WHERE  id = 2;
UPDATE event_category SET  label =  'Geburtstage', color = '', cat_order = 3 WHERE  id = 10;
UPDATE event_category SET  label =  'Inter-Data', color = '', cat_order = 4 WHERE  id = 11;
UPDATE event_category SET  label =  'Büro', color = '', cat_order = 5 WHERE  id = 12;
COMMIT;