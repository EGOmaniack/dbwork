<?php


// var_dump($all_works2);
// exit;

$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($all_works2 as $work) {
    // var_dump($work);
    // exit;
    if( $work['razdel'] == false || substr_count($work['pp'], ".") > 0 ){
        $sqlstr = "insert into repair_stuff.repair_jobs ( razdel, subparagraph, name,  replacement ) ";
        $sqlstr .= "select ws.id, '".$work['pp']."', '".$work['name']."', ".($work['replace'] == false ? 'false' : 'true')." ";
        $sqlstr .= "from repair_stuff.work_sections ws ";
        $sqlstr .= 'where ws."name" = \''.$work['parent'].'\' ;';
        // $sqlstr .= "and wt.code = '".$work['code']."' ;";
        // echo $sqlstr;
        // exit;
        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
    }
}

pg_free_result($result);
pg_close($dbconn);

echo __FILE__.'   done';
?>
