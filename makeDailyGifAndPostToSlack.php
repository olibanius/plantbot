<?php
if ($argv[1] == 'yesterday') {
	$date = date('Y-m-d', strtotime('yesterday'));
} else {
	$date = date("Y-m-d");
}
$filename = trim(shell_exec("php /home/pi/plantbot/makeDailyGif.php $date"));
shell_exec("php /home/pi/plantbot/dropboxUploadFile.php $filename");
$link = shell_exec("php /home/pi/plantbot/dropboxShareLink.php $filename");
shell_exec("php /home/pi/plantbot/postFileToSlack.php $link");
