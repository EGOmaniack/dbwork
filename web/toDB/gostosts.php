<?php

// var_dump($all_works);

/* Упомянутые документы */
$documents = [];
foreach($all_works as $razdel){
    foreach ($razdel as $work) {
        foreach ($work['detail'] as $detail) {
            foreach ($detail['gost'] as $value) {
                if($value != "") {
                    $document = [];
                    $document['document'] = trim($value);
                    $document['det_name'] = $detail['name'];
                    $documents[] =$document;
                    unset($document);
                }
            }
        }
    }
}
// var_dump($documents);

$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($documents as $value) {
    $sqlstr = 'insert into repair_stuff.gostost ( det_id, name ) ';
    $sqlstr .= "select con.id , '".$value['document']."' ";
    $sqlstr .= "from repair_stuff.\"Consumables\" con ";
    $sqlstr .= "where con.\"name\" = '".$value['det_name']."' ";
    $sqlstr .= ";";
    // echo $sqlstr;
    // exit;
    $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());

}



pg_free_result($result);
pg_close($dbconn);

echo 'done';

?>
