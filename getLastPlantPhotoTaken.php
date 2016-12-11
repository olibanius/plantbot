<?php

$today = date('Ymd');;
$files = array();
chdir(getcwd().'/daily_images/');
foreach (glob("plantbot-$today*.jpg") as $filename) {
    $files[] = $filename;
}
if (!empty($files)) {
    echo getcwd().'/'.end($files);
} else {
    echo false;    
}
