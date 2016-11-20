<?php

if (!(is_file('settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file('settings.txt');

include('db-model.php');
$db = new Db_model;
$data = $db->getPlantsData();
$text = '';
foreach ($data as $plant) {
    $text .= $plant['nickname']."\n";
    $text .= "Senast matad: ".substr($plant['last_feed_time'], 0, -3)."\n";
    $text .= "Fick uppmärksamhet: ".substr($plant['data']['time'], 0, -3)."\n";
    $text .= "Ålder: ".$plant['data']['age_days']." dagar\n";
    $text .= "Jordens torrhet: ".$plant['data']['soil']." milliohm(?)\n";
    $text .= "Temperatur: ".$plant['data']['temp']." grader\n";
    $text .= "\n";
}
echo $text;

