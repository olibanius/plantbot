<?php

$filename = trim(shell_exec('python snap.py'));
$filename_arr = explode('/', $filename);
shell_exec("/home/pi/Dropbox-Uploader/dropbox_uploader.sh upload /home/pi/plantbot/$filename ".end($filename_arr));
$shareOutput = trim(shell_exec("/home/pi/Dropbox-Uploader/dropbox_uploader.sh share ".end($filename_arr)));
$link = substr($shareOutput, strpos($shareOutput, 'https'));
$link = substr($link, 0, -1)."1";
shell_exec("php /home/pi/plantbot/slack-daily.php $link");
