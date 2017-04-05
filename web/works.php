<?php
ini_set('display_errors', 0) ;
ini_set('xdebug.var_display_max_depth', 10);
// ini_set('xdebug.var_display_max_children', 10);
ini_set('xdebug.var_display_max_data', 1024);

$filename = './files/works.csv';
$all_works = []; /* Старый вариант (не используется) */

$topFather = array("main_t2"); /* Массив, по которому отслеживается ближайший родитель строки */
$topFatherCount = 1; /* число помогающее соблюдать порядок при работе с topFather */
$lastRazdelCount = 0; /* число точек в названии пункта предыдущей записи, являющейся разделом */
$lastRazdel = "main"; /* Наименование предыдущего раздела для цикла */
$RazdelCandidat; /* Сюда запоминается название каждой строки на случай если на следующей строке выяснится, что это был раздел */
$all_works2 = []; /* сюда складывается вся информация */
$rate=1;
/* Первая версия */
/*Считываем данные в $all_works*/
    // if(file_exists($filename)) {
    //     $handle = fopen($filename, "r");
    //     if($handle) {
    //         $head = true;
    //         $razdel = "";
    //         $work = [];
    //         while(($line = fgets($handle)) != false) {
    //             $token = explode( ";", $line );
    //             if(!$head) { /** пропускаем шапку таблицы */
    //                 if($token[0] != ""){
    //                     if(strpos($token[0],".") === false){
    //                         if($razdel == "") { /* Первый раздел */
    //                             $razdel = $token[1];
    //                         } else { /* остальные разделы */
    //                             $all_works[$razdel] = $works;
    //                             unset($works);
    //                             $razdel = $token[1];
    //                         }
    //                     } else {
    //                         $razdel_name = $token[1];
    //                         $work['pp'] = mb_substr($token[0],2);
    //                         $work['name'] = $token[1];
    //                         $work['replace'] = $token[2] == "+" ? true : false;
    //                         $work['code'] = $token[3] == "" ? 'other' : $token[3];
    //                         $work['detail'][0]['name'] = $token[5];
    //                         $work['detail'][0]['mark'] = $token[6];
    //                         if(strpos($token[7],",") === false) {
    //                             $work['detail'][0]['gost'][] = $token[7];
    //                         } else {
    //                             $work['detail'][0]['gost'] = explode(",",$token[7]);
    //                         }
    //                         $work['detail'][0]['unit'] = $token[9];
    //                         $work['detail'][0]['rate'] = floatval($token[10]);

    //                         $works[] = $work;
    //                         unset($work);
    //                     }
    //                 } /* Конец if цифры нет в 1ой ячейке */
    //                 else{
    //                     $detail['name'] = $token[5];
    //                     $detail['mark'] = $token[6];
    //                     if(strpos($token[7],",") === false) {
    //                         $detail['gost'][] = $token[7];
    //                     } else {
    //                         $detail['gost'] = explode(",",$token[7]);
    //                     }
    //                     $detail['unit'] = $token[9];
    //                     $detail['rate'] = floatval($token[10]);

    //                     $works[count($works)-1]['detail'][] = $detail;
    //                     unset($detail);
    //                 }
    //             }
    //             if($token[1] == "2"){ $head = false; } /* Нашли шапку таблицы */
    //         } /* кончились строки */
    //         $all_works[$razdel] = $works;
    //     }    
    // }
/* Вторая версия */
if(file_exists($filename)) {
    $handle = fopen($filename, "r");
    if($handle) {
        $head = true; /* пока head true мы находимся в шапке таблице */
        $razdel = "";
        $work = []; /* Сюда складывается каждая строка разложенная на элементы */
        while(($line = fgets($handle)) != false) {
            $token = explode( ";", $line );
            if(!$head) { /** пропускаем шапку таблицы */
                if($token[0] != ""){ /* Строка начинается с нового пункта */
                    if( $lastRazdelCount != substr_count($token[0], ".") ){ /* Число точчек в строке изменилось */
                        if( $lastRazdelCount < substr_count($token[0], ".") ) { /* точек стало больше */
                            $lastRazdel = $RazdelCandidat != null ? $RazdelCandidat : $token[1];
                            //echo "<p style='color:red; margin: 0;'>раздел $lastRazdel</p>";
                            $topFather[$topFatherCount] = $lastRazdel;
                            $topFatherCount++;
                            $all_works2[count($all_works2)-1]['razdel'] = true;
                            //var_dump($topFather);
                        } else {
                            if( $lastRazdelCount > substr_count($token[0], ".") ) { /* точек стало меньше */
                                $diff = $lastRazdelCount - substr_count($token[0], ".");
                                for ( $i=0; $i<$diff; $i++ ) {
                                    unset($topFather[count($topFather)-1]);
                                    $topFatherCount--;
                                }
                                //$work['razdel'] = true;
                            }
                            //echo "Мой предок - ".$topFather[count($topFather)-1]."  ";
                        }
                        /* Количество точек отличается - прямой кандидат быть названием раздела  */
                        $lastRazdelCount = substr_count($token[0], ".");
                    } else {
                        //echo "-";
                    }
                    if($token[0] == "1") $work['razdel'] = true;
                    $work['pp'] = $token[0];
                    $work['parent'] = $topFather[count($topFather)-1];
                    $work['name'] = $token[1];
                    $work['replace'] = $token[2] == "+" ? true : false;
                    $work['code'] = $token[3] == "" ? 'other' : $token[3];
                    $work['cost'] = 100 * $rate++;
                    /* Присваиваем как минимум одну деталь. Остальные присвоим если строка не начинается с цифры */
                    $work['detail'][0]['name'] = $token[5];
                                $work['detail'][0]['mark'] = $token[6];
                                $work['detail'][0]['gost'] = $token[7];
                                $work['detail'][0]['size'] = $token[8];
                                $work['detail'][0]['unit'] = $token[9];
                                $work['detail'][0]['rate'] = floatval($token[10]);
                    //echo /*"n=".$lastRazdelCount.*/" pp = ".$token[0]." tF- ".$topFather[count($topFather)-1]."<br />";
                    $RazdelCandidat = $token[1];
                    
                } else { /* Конец if цифры нет в 1ой ячейке */
                    $detail['name'] = $token[5];
                    $detail['mark'] = $token[6];
                    $detail['gost'] = $token[7];
                    $detail['size'] = $token[8];
                    $detail['unit'] = $token[9];
                    $detail['rate'] = floatval($token[10]);
                    //var_dump($detail);
                     $all_works2[count($all_works2)-1]['detail'][] = $detail;
                     unset($detail);
                }
            }
            if($token[1] == "2"){ $head = false; } /* Нашли шапку таблицы */
            if($work != null) {
                if(!isset($work['razdel'])) $work['razdel'] = false;
                $all_works2[] = $work;
            }
            unset($work);
        } /* кончились строки */
        //$all_works[$razdel] = $works;
    }    
}

// var_dump($all_works2);
// exit;

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
        <a href="/index.php">Назад</a>
    </div>
</body>
</html>