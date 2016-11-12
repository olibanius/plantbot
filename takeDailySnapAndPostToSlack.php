<?php
$filename = trim(shell_exec('python /home/pi/plantbot/snap.py'));
shell_exec("php /home/pi/plantbot/dropboxUploadFile.php $filename");
$link = shell_exec("php /home/pi/plantbot/dropboxShareLink.php $filename");
shell_exec("php /home/pi/plantbot/postFileToSlack.php $link");
