<?php

$text = $argv[1];
$link = $argv[2];

$year = date('Y', strtotime('yesterday'));
$month = date('m', strtotime('yesterday'));
$day = date('d', strtotime('yesterday'));

chdir(getcwd().'/daily_images/');
foreach (glob("plantbot-$year$month$day-12*.jpg") as $filename) {
    $files[] = $filename;
}
include('mailFile.php');
$recipients = "fredrik.safsten@gmail.com, weiland_helena@hotmail.com, kristina.safsten@ovikenergi.se";
$file = getcwd()."/daily_images/plantbot-2016-1103-112.jpg";
mail_attachment($files[0], '', 'fredrik.safsten@gmail.com', 'plantbot@safstens', 'Plantbot', 'noreply@safstens', "$text", "Hej! Så här fina är älsklingarna nu. Klicka på denna länk för att se en animation hur det gått denna månad: $link");
