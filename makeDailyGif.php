<?php
$date = $argv[1];
$path = "/home/pi/plantbot/daily_images/";
$filename_noSuffix = "plantbot-".str_replace('-', '', $date);
shell_exec("convert -delay 20 -loop 0 $path$filename_noSuffix* $path$filename_noSuffix.gif");
echo "$path$filename_noSuffix.gif";
