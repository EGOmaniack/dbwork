<?php

// var_dump($all_works2);

$local_works = [];

foreach ($all_works2 as $work) {

        // $local_work = $work;
        $local_work['name'] = $work['name'];
        $local_work['parent'] = $work['parent'];
        $local_work['razdel'] = $work['razdel'];
        unset($local_work['code']);
        $codes = explode(",", $work['code']);
        foreach ($codes as $value) {
            $local_work['code'][] = trim($value);
        }
        $local_works[] = $local_work;

}
// var_dump($local_works);


$dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

foreach ($local_works as $work) {
    // var_dump($work);
    // exit;
    foreach ($work['code'] as $value) {
        if( $work['razdel'] == false || substr_count($work['pp'], ".") > 0 ){
            $sqlstr  = "insert into repair_stuff.rep_jobs_docs ( job_id, doc_type ) ";
            $sqlstr .= "select rj.id, dt.id ";
            $sqlstr .= "from repair_stuff.repair_jobs rj, ";
            $sqlstr .= "	repair_stuff.doc_type dt ";
            $sqlstr .= "where rj.\"name\" = '".$work['name']."' ";
            $sqlstr .= "and rj.razdel = getWorkSection('".$work['parent']."') ";
            $sqlstr .= "and dt.code = '".$value."' ";
            $sqlstr .= " ;";
            // $sqlstr .= "and wt.code = '".$work['code']."' ;";
            // echo $sqlstr;
            // exit;
            $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
        }
    }
}

pg_free_result($result);
pg_close($dbconn);

echo __FILE__.'   done';

?>
