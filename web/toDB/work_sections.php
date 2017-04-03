<?php
/* Разделы работ */
$razdels = [];
foreach($all_works as $key => $razdel){
    $irazdel['name'] = $key;
    $irazdel['cost'] = (count($razdels)+1) * 1000;

    $razdels[] = $irazdel;
    unset($irazdel);
}
var_dump($razdels);

/* Засылать все одной пачкой не выйдет. Надо выяснять id родителя для каждой строки
$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());


$sqlstr = "insert into repair_stuff.work_sections ";
$sqlstr .= "( name, weight ) values ";

foreach ($razdels as $key => $rzd) {
    if($key < count($razdels)-1){
        $sqlstr .= "('".$rzd['name']."', '".$rzd['cost']."' ), ";
    }
}

$sqlstr .= "('".$razdels[count($razdels)-1]['name']."', '".$razdels[count($razdels)-1]['cost']."' ) ";

$sqlstr .= ";";

$result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
*/

pg_free_result($result);
pg_close($dbconn);

echo 'done';
?>