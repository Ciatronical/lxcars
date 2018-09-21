<?php
require_once __DIR__.'/../../inc/stdLib.php';//Prüfen!!!!!!

printArray( $GLOBALS['dbh']->getOne( "SELECT EXISTS  ( SELECT 1 FROM   information_schema.tables WHERE  table_schema = 'public' AND  table_name = 'crm') AS crm_exist " ) );

printArray( $GLOBALS['dbh']->getOne( " WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id )  SELECT ( SELECT sonumber FROM tmp), 1126, 861,  customer.taxzone_id, customer.currency_id, 14 FROM customer WHERE customer.id = '1126' RETURNING id "));


//printArray( $GLOBALS['dbh']->getAll( "SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx"));// SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx

//printArray( $GLOBALS['dbh']->getAll( "SELECT * FROM oe WHERE c_id = 14 "));

$units = $GLOBALS['dbh']->getAll( "SELECT name,type FROM units", true );
$accountingGroups = $GLOBALS['dbh']->getAll( "SELECT id, description FROM buchungsgruppen ORDER BY id = ( SELECT buchungsgruppen_id FROM ( SELECT buchungsgruppen_id, count( buchungsgruppen_id ) AS id FROM parts GROUP BY 1 ORDER BY id DESC LIMIT 1 ) AS nothing ) DESC", true );
$customerHourlyRate = $GLOBALS['dbh']->getOne( "SELECT customer_hourly_rate FROM defaults",true );
$taxzones = $GLOBALS['dbh']->getALL( "SELECT id, description FROM tax_zones Order by sortkey ASC", true );

/*
    $output = array(
        "units" => $units,
        "accountingGroups" => $accountingGroups,
        "customerHourlyRate" => $customerHourlyRate,
        "taxzones" => $taxzones
    );
    echo $units;
*/

printArray( $units );

$result['units'] = $units;
$result['accountingGroups'] = $accountingGroups;

printArray( $result ); //Klappt .....





//So nun holen wir mal alles mit einer Query anstatt mit drei Abragen

/*
SELECT json_agg( i ) FROM ( SELECT * FROM users ) i;
SELECT json_agg( j ) FROM ( SELECT * FROM groups ) j;

explain analyze select json_agg (my_json) from (select row_to_json(x) as my_json from (SELECT json_agg( i ) as groups FROM ( SELECT * FROM groups ) i) x union all select row_to_json(x) from (SELECT json_agg( i ) as users FROM ( SELECT * FROM users ) i) x) bla;

*/

// Klappt noch viel, viel schneller.....

?>