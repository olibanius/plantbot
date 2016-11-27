<?php

$text = $argv[1];
$link = $argv[2];

include('db-model.php');
$db = new Db_model;
$startdate = date('Y-m-d', strtotime('first day of last month'));
$enddate = date('Y-m-d', strtotime('last day of last month'));
$arr = $db->getFeedingStats($startdate, $enddate);

$plants = $db->getPlants();
$output = "\n\n";
foreach ($arr as $plantId => $entries) {
    $output .= $plants[$plantId]['nickname']."\n";
    foreach ($entries as $entry) {
        $output .=  date('d M H:i', strtotime($entry['time'])) .
                    " (soil ".$entry['soil'].", temp ".$entry['temp'].")" .
                    "\n";
    }
    $output .= "\n";
}

$year = date('Y', strtotime('yesterday'));
$month = date('m', strtotime('yesterday'));
$day = date('d', strtotime('yesterday'));

chdir(getcwd().'/daily_images/');
foreach (glob("plantbot-$year$month$day-12*.jpg") as $filename) {
    $files[] = $filename;
}
include('mailFile.php');
$recipients = "fredrik.safsten@gmail.com, weiland_helena@hotmail.com, kristina.safsten@ovikenergi.se";
//$recipients = "fredrik.safsten@gmail.com";
$file = getcwd()."/daily_images/plantbot-2016-1103-112.jpg";
mail_attachment($files[0], '', "$recipients", 'plantbot@safstens', 'Plantbot', 'noreply@safstens', "$text", "Hej! Så här fina är älsklingarna nu. Klicka på denna länk för att se en animation hur det gått denna månad: $link $output");
