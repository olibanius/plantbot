<?php
include('db-model.php');
$db = new Db_model;
$workingDir = getcwd();
$filename = trim(shell_exec("php $workingDir/getLastPlantPhotoTaken.php"));
if (!$filename) {
    die ('No recent photo found, sorry');
}
$link = $db->getShareLink($filename);
shell_exec("php $workingDir/postToSlack.php 'Här kommer senast tagna bild!' $link");
