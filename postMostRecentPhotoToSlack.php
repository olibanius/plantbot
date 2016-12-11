<?php
$workingDir = getcwd();
$filename = trim(shell_exec("php $workingDir/getLastPlantPhotoTaken.php"));
if (!$filename) {
    die ('No recent photo found, sorry');
}
shell_exec("php $workingDir/dropboxUploadFile.php $filename");
$link = shell_exec("php $workingDir/dropboxShareLink.php $filename");
shell_exec("php $workingDir/postToSlack.php 'Här kommer senast tagna bild!' $link");
