<?php
ini_set('display_errors', 0) ;
ini_set('xdebug.var_display_max_depth', 10);
// ini_set('xdebug.var_display_max_children', 10);
ini_set('xdebug.var_display_max_data', 1024);

$filename = './files/works.csv';
$all_works = [];

if(file_exists($filename)) {
    $handle = fopen($filename, "r");
    if($handle) {
        $head = true;
        $razdel = "";
        $work = [];
        while(($line = fgets($handle)) != false) {
            $token = explode( ";", $line );
            if(!$head) { /** пропускаем шапку таблицы */
                if($token[0] != ""){
                    if(strpos($token[0],".") === false){
                        if($razdel == "") { /* Первый раздел */
                            $razdel = $token[1];
                        } else { /* остальные разделы */
                            $all_works[$razdel] = $works;
                            unset($works);
                            $razdel = $token[1];
                        }
                    } else {
                        $work['pp'] = mb_substr($token[0],2);
                        $work['name'] = $token[1];
                        $work['code'] = $token[2] == "" ? 'other' : $token[2];
                        $work['detail'][0]['name'] = $token[4];
                        $work['detail'][0]['mark'] = $token[5];
                        if(strpos($token[6],",") === false) {
                            $work['detail'][0]['gost'][] = $token[6];
                        } else {
                            $work['detail'][0]['gost'] = explode(",",$token[6]);
                        }
                        $work['detail'][0]['unit'] = $token[8];
                        $work['detail'][0]['rate'] = floatval($token[9]);

                        $works[] = $work;
                        unset($work);
                    }
                } /* Конец if цифры нет в 1ой ячейке */
                else{
                    $detail['name'] = $token[4];
                    $detail['mark'] = $token[5];
                    if(strpos($token[6],",") === false) {
                        $detail['gost'][] = $token[6];
                    } else {
                        $detail['gost'] = explode(",",$token[6]);
                    }
                    $detail['unit'] = $token[8];
                    $detail['rate'] = floatval($token[9]);

                    $works[count($works)-1]['detail'][] = $detail;
                    unset($detail);
                }
            }
            if($token[1] == "2"){ $head = false; } /* Нашли шапку таблицы */
        } /* кончились строки */
        $all_works[$razdel] = $works;
    }    
}
// var_dump($all_works);

/* Разделы работ */
$razdels = [];
foreach($all_works as $key => $razdel){
    $irazdel['name'] = $key;
    $irazdel['cost'] = (count($razdels)+1) * 1000;

    $razdels[] = $irazdel;
    unset($irazdel);
}
// var_dump($razdels);

/* Упомянутые документы */
$documents = [];
foreach($all_works as $razdel){
    foreach ($razdel as $work) {
        foreach ($work['detail'] as $detail) {
            foreach ($detail['gost'] as $value) {
                if($value != "") $documents[] = trim($value);
            }
        }
    }
}
var_dump($documents);

/* Перечень всех деталей */
$details = [];
foreach($all_works as $razdel){
    foreach ($razdel as $value) {
        foreach ($value['detail'] as $det) {
            $details[] = $det;
        }
    }
}
// var_dump($details);

function insert_emp($filename) {

    $dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

    $emps=[];

    if(file_exists($filename)) {
        $handle = fopen($filename, "r");
        if($handle) {
            while(($line = fgets($handle)) != false) {
                $emp = [];
                $token = explode( ";", $line );
                    
                foreach($token as $key => $value) {
                    if($key == 1) {
                        $name = explode(" ", $value);
                        $emp[] = $name[0];
                        $emp[] = $name[1];
                        $emp[] = $name[2];
                    } else{
                        $emp[] = $value;
                    }
                }
                $emps[] = $emp;
            }
            
        }
    
    //var_dump($emps);
    foreach($emps as $employeer) {
        $sqlstr = "insert into staff.employees ( personal_number, surname, name, middle_name ,position, bday) select ";
        $sqlstr .= "'".$employeer[0]."', '".$employeer[1]."', '".$employeer[2]."', '".$employeer[3]."', p.id".", '".$employeer[5]."' ";
        $sqlstr .= "from staff.w_position p where p.worker_position = '".$employeer[4]."';";

        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());
    }


    pg_free_result($result);
    pg_close($dbconn);
    echo '<br>done<br>';
    }else{
            echo "<br>"."файла thelist.csv не вижу";
    }
}
?>