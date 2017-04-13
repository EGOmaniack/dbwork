<?php
$filename = './files/smazka.csv';
$lubs = [];
if(file_exists($filename)) {
    $handle = fopen($filename, "r");
    if($handle) {
        while(($line = fgets($handle)) != false) {
            $token = explode(";", $line);
            $lub['name'] = $token[0];
            $lub['points_count'] = $token[1];
            $lub['gsm_name'] = $token[2];
            $lub['lub_method'] = $token[3];
            $lub['mass'] = floatval(str_replace(",", ".", $token[4]));

            $lubs[] = $lub;
            unset($lub);
        }
    }
}

// var_dump($lubs);
// exit;
$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($lubs as $value) {
    $sqlstr  = "insert into repair_stuff.repair_jobs ( razdel, name ) ";
    $sqlstr .= "values ( 4, '".$value['name']."' ) ";
    $sqlstr .= ";  ";

    $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
}

foreach ($lubs as $lub) {
    $sqlstr  = "insert into repair_stuff.smazka ( job_id, points_count, gsm_name, lub_method, mass ) ";
    $sqlstr .= "select rj.id, ".$lub['points_count'].", '".$lub['gsm_name']."', '".$lub['lub_method']."', ".$lub['mass']." ";
    $sqlstr .= "from repair_stuff.repair_jobs rj ";
    $sqlstr .= "where rj.\"name\" = '".$lub['name']."' ";
    $sqlstr .= ";";

    // echo $sqlstr;
    // exit;

    $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
}
pg_free_result($result);
pg_close($dbconn);
echo 'done';
?>