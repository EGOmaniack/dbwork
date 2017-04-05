<?php
/* Разделы работ */
// var_dump($all_works2);
$razdels = [];
foreach($all_works2 as $razdel){
    if( $razdel['razdel'] ){
        $raz['pp'] = $razdel['pp'];
        $raz['parent'] = $razdel['parent'];
        $raz['name'] = $razdel['name'];
        $raz['weight'] = $razdel['cost'];

        $razdels[] = $raz;
        unset($raz);
    }
}
// var_dump($razdels);

$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($razdels as $section) {

        $sqlstr = "insert into repair_stuff.work_sections ( name, weight, r_type_id, platform_type_id, parent_sec ) ";
        $sqlstr .= "select '".$section['name']."', ".$section['weight'].", rt.id, 1, parent.id ";
        $sqlstr .= "from platforms.repair_type rt,  ";
        $sqlstr .= "repair_stuff.work_sections parent ";
        $sqlstr .= "where rt.code = 't2' ";
        $sqlstr .= "and parent.name = '".$section['parent']."' ";
        $sqlstr .= ';';
        // $sqlstr .= "and wt.code = '".$work['code']."' ;";
        // echo $sqlstr;
        // exit;
        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
        // exit;
        usleep(2500);
}

pg_free_result($result);
pg_close($dbconn);

echo __FILE__.'  done';
?>