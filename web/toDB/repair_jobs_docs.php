<?php

$local_works = [];
foreach ($all_works as $key => $section) {
    foreach ($section as $work) {
        $local_work = $work;
        unset($local_work['code']);
        $codes = explode(",", $work['code']);
        foreach ($codes as $value) {
            $local_work['code'][] = trim($value);
        }
        $local_work['razdel'] = $key;
        $local_works[] = $local_work;
    }
}
var_dump($local_works);

?>