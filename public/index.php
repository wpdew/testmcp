<?php

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

$date = date('Y-m-d H:i:s');
dd($date);