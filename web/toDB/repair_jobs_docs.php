<?php

// var_dump($all_works2);

$local_works = [];

foreach ($all_works2 as $work) {

        // $local_work = $work;
        $local_work['name'] = $work['name'];
        unset($local_work['code']);
        $codes = explode(",", $work['code']);
        foreach ($codes as $value) {
            $local_work['code'][] = trim($value);
        }
        $local_works[] = $local_work;

}
var_dump($local_works);

?>