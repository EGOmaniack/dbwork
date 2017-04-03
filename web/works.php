<?php
ini_set('display_errors', 0) ;
ini_set('xdebug.var_display_max_depth', 10);
// ini_set('xdebug.var_display_max_children', 10);
ini_set('xdebug.var_display_max_data', 1024);

$filename = './files/works.csv';
$all_works = [];

/*Считываем данные в $all_works*/
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
                        $work['replace'] = $token[2] == "+" ? true : false;
                        $work['code'] = $token[3] == "" ? 'other' : $token[3];
                        $work['detail'][0]['name'] = $token[5];
                        $work['detail'][0]['mark'] = $token[6];
                        if(strpos($token[7],",") === false) {
                            $work['detail'][0]['gost'][] = $token[7];
                        } else {
                            $work['detail'][0]['gost'] = explode(",",$token[7]);
                        }
                        $work['detail'][0]['unit'] = $token[9];
                        $work['detail'][0]['rate'] = floatval($token[10]);

                        $works[] = $work;
                        unset($work);
                    }
                } /* Конец if цифры нет в 1ой ячейке */
                else{
                    $detail['name'] = $token[5];
                    $detail['mark'] = $token[6];
                    if(strpos($token[7],",") === false) {
                        $detail['gost'][] = $token[7];
                    } else {
                        $detail['gost'] = explode(",",$token[7]);
                    }
                    $detail['unit'] = $token[9];
                    $detail['rate'] = floatval($token[10]);

                    $works[count($works)-1]['detail'][] = $detail;
                    unset($detail);
                }
            }
            if($token[1] == "2"){ $head = false; } /* Нашли шапку таблицы */
        } /* кончились строки */
        $all_works[$razdel] = $works;
    }    
}
//  var_dump($all_works);

include_once './toDB/'.$_GET['flow'].'.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>jobs</title>
</head>
<body>
    <div class="wraper">
        <a href="/works.php/?flow=work_sections">1 - Разделы</a><br>
        <a href="/works.php/?flow=repair_jobs">2 - перечень работ</a><br>
        <a href="/works.php/?flow=repair_jobs_docs">3 - Типы документов</a><br>
        <a href="/works.php/?flow=details">4 - Расходники(детали)</a><br>
        <a href="/works.php/?flow=gostosts">5 - ГОСТы и ОСТы</a><br>
        <a href="/index.php">Назад</a>
    </div>
</body>
</html>