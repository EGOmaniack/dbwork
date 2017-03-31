<?php


// var_dump($all_works);
// exit;

$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($all_works as $key => $section) {
    foreach ($section as $work) {
        // var_dump($work);
        // exit;

        $sqlstr = "insert into repair_stuff.repair_jobs ( razdel, subparagraph, name, work_doc_type, replacement ) ";
        $sqlstr .= "select ws.id, '".$work['pp']."', '".$work['name']."', wt.id, ".($work['replace'] == false ? 'false' : 'true')." ";
        $sqlstr .= "from repair_stuff.work_sections ws, repair_stuff.doc_type wt ";
        $sqlstr .= 'where ws."name" = \''.$key.'\' ';
        $sqlstr .= "and wt.code = '".$work['code']."' ;";
        // echo $sqlstr;
        // exit;
        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
    }
}

pg_free_result($result);
pg_close($dbconn);

echo 'done';
?>
