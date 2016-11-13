<?php
$plantData = shell_exec("php /home/pi/plantbot/getPlantData.php");
shell_exec("php /home/pi/plantbot/postToSlack.php '$plantData'");
