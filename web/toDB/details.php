<?php

/* Перечень всех деталей */
$details = [];
foreach($all_works as $razdel){
    foreach ($razdel as $job) {
        foreach ($job['detail'] as $det) {
            $dett = $det;
            $dett['job'] = $job['name'];
            $details[] = $dett;
            unset($dett);
        }
    }
}
// var_dump($details);
// exit;

$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($details as $value) {
    if($value['name'] != ''){
        $sqlstr = 'insert into repair_stuff."Consumables" ( id_work, name, mark, consumption_rate ) ';
        $sqlstr .= "select rj.id, '".$value['name']."', '".$value['mark']."', '".( $value['rate'] == '' ? '0' : $value['rate'] )."' ";
        $sqlstr .= "from repair_stuff.repair_jobs rj ";
        $sqlstr .= "where rj.\"name\" = '".$value['job']."' ";
        $sqlstr .= ";";
        // echo $sqlstr;
        // exit;
        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
    }
}



pg_free_result($result);
pg_close($dbconn);

echo 'done';
?>
