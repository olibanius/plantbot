<?php
$filename = trim(shell_exec('php /home/pi/plantbot/makeMonthlyGif.php'));
shell_exec("php /home/pi/plantbot/dropboxUploadFile.php daily_images/$filename");
$link = shell_exec("php /home/pi/plantbot/dropboxShareLink.php $filename");
shell_exec("php /home/pi/plantbot/postToSlack.php 'En månad med älsklingarna!' $link");
$linkNoDL = substr($link, 0, -1)."0";
shell_exec("php /home/pi/plantbot/mailMonthlyGif.php 'En månad med älsklingarna!' $linkNoDL");
