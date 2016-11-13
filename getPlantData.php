<?php

if (!(is_file('settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file('settings.txt');

include('db-model.php');
$db = new Db_model;
$data = $db->getPlantsData();
$text = '';
foreach ($data as $plant) {
    $text .= $plant['nickname']."\n";
    $text .= "Senast matad: ".$plant['last_feed_time']."\n";
    $text .= "Ålder: ".$plant['data']['age_days']."\n";
    $text .= "Jordens torrhet: ".$plant['data']['soil']."\n";
    $text .= "Temperatur: ".$plant['data']['temp']."\n";
}
echo $text;

