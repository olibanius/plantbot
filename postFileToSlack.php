<?php
$link = $argv[1];
shell_exec("php /home/pi/plantbot/slack-daily.php $link");
