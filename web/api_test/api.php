<?php

$data = file_get_contents('https://free.currconv.com/api/v7/convert?q=USD_JPY&compact=ultra&apiKey=f603ebb5d1a5a0b6e8c0');
var_dump($data);
// $data = json_decode($data, true);
// $date_time = file_get_contents('https://free.currconv.com/others/usage?apiKey=f603ebb5d1a5a0b6e8c0');
// $date_time = json_decode($date_time, true);
// $date = substr($date_time["timestamp"], 0,  strpos($date_time["timestamp"], 'T'));
// $time = substr($date_time["timestamp"], 11, 8);
// var_dump($time);
// var_dump($date . " " . $time);
// // $data = $data + $date;
// // var_dump($data);
// date_default_timezone_set("Europe/London");
// $time = date('Y-m-d H:i:s');
// var_dump($time);
?>