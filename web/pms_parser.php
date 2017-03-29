<?php
ini_set('display_errors', 0) ;
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 10);
ini_set('xdebug.var_display_max_data', 1024);

if($_GET['flow']=="pms") insert_pmss('./files/pmss.txt');

function insert_pmss($filename){

    $dbconn = pg_connect("host=localhost port=5432 dbname=platforms user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

    $sqlstr = "insert into firms.companies(number, name, comment) values ";

    $pmss=[]; /*массив пмсов*/
    
    if(file_exists($filename)){

        $handle = fopen($filename,"r");
            if($handle){
                while(($line = fgets($handle)) !== false){
                $token = explode(" ",$line);
                $pms=[];
                $pms['number']=$token[0];
                $other="";
                foreach($token as $key => $tok) {
                    if($key != 0) {
                        $other .=" ".$tok;
                    }
                }
                $other = trim($other);
                $oth = explode(";",$other);
                $pms['name'] = $oth[0];
                $pms['comment'] = str_replace(")","",str_replace("(","",$oth[1]));

                $pmss[]=$pms;
            }
            
            fclose($handle);
        }else{
            echo "<br>"."файла thelist.csv не вижу";
        }
    }
    //var_dump($pmss);

    foreach($pmss as $key => $value) {
        $sqlstr .= "('".$pmss[$key]['number']."',"." '".$pmss[$key]['name']."', '".$pmss[$key]['comment']."')";
        if($key+1 != count($pmss)) $sqlstr .= ",";
    }

    // for($i = 0; $i < count($profs); $i++ ) {
    //     $sqlstr .= "('".$profs[$i]."')";
    //     if($i+1 != count($profs)) $sqlstr .= ",";
    // }

    //echo $sqlstr;

    $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());

    pg_free_result($result);
    pg_close($dbconn);
    echo 'done';
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>ПМСы</title>
</head>
<body>
    <div class="wrapper">
        <a href="pms_parser.php/?flow=pms">Сканировать ПМС</a><br>
        <a href="/index.php">Назад</a>
    </div>
</body>
</html>