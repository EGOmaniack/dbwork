<?php
ini_set('display_errors', 0) ;
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

if($_GET['flow']=="professions") insert_professions("./files/thelist.csv");
if($_GET['flow']=="employeers") insert_emp("./files/thelist.csv");
if($_GET['flow']=="workshop") workshop("./files/workshop.txt");

function insert_professions($filename){

    $dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

    $sqlstr = "insert into staff.w_position(worker_position) values";

    $profs=[]; /*массив профессий*/
    
    if(file_exists($filename)){

        $handle = fopen($filename,"r");
            if($handle){
                while(($line = fgets($handle)) !== false){
                $token = explode(";",$line);
                //$sqlstr .= "('".$token[2]."'), ";//, ";
                if(!in_array($token[2], $profs)) {
                    $profs[] = $token[2];
                }
            }
            fclose($handle);
        
        }
    // var_dump($profs);

    for($i = 0; $i < count($profs); $i++ ) {
        $sqlstr .= "('".$profs[$i]."')";
        if($i+1 != count($profs)) $sqlstr .= ",";
    }

    // echo $sqlstr;

    $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());

    pg_free_result($result);
    pg_close($dbconn);
    echo '<br><p style="color=green;">done</p><br>';
    }else{
            echo "<br>"."файла thelist.csv не вижу";
    }
}

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
    echo '<br><p style="color=blue;">done</p><br>';
    }else{
            echo "<br>"."файла thelist.csv не вижу";
    }
}

function workshop($filename){

    $dbconn = pg_connect("host=localhost port=5432 dbname=platformDocs user=postgres password=Rgrur4frg56eq16")
    or die('Could not connect: ' . pg_last_error());

    $sqlstr = "";

    $surnames=[]; /*массив профессий*/
    
    if(file_exists($filename)){

        $handle = fopen($filename,"r");
            if($handle){
                while(($line = fgets($handle)) !== false){
                $token = explode(" ",$line);
                $surnames[] = $token;
            }
            fclose($handle);
        
        }
    // var_dump($surnames);

    foreach ($surnames as $value) {
        $sqlstr = "insert into staff.workshop ( emp_id ) ";
        $sqlstr .="select p.id ";
        $sqlstr .="from staff.employees p ";
        $sqlstr .="where p.surname = '".$value[0]."'";
        $sqlstr .="and substring(p.name from 1 for 1) = substring( '".$value[1]."' from 1 for 1);";

        $result = pg_query($dbconn, $sqlstr) or die('Ошибка запроса: ' . pg_last_error());

    }
        echo '<br><p style="color=red;">done</p><br>';
        }else{
                echo "<br>"."файла workshop.txt не вижу";
        }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Инициализировать сотрудников в БД</title>
</head>
<body>
    <div class="wraper">
        <a href="/emp_init.php/?flow=professions">Professions</a><br />
        <a href="/emp_init.php/?flow=employeers">Employeers</a><br />
        <a href="/emp_init.php/?flow=workshop">Workshop</a><br />
        <a href="/index.php">Назад</a>
    </div>
</body>
</html>