<?php
$fileNames = array();
$year = date('Y');
$month = date('m');

chdir(getcwd().'/daily_images/');
foreach (glob("plantbot-$year*-$month*.jpg") as $filename) {
    $files[] = $filename;
}
$fileName = "monthgif-$year-$month.gif";
$files_str = implode(' ', $files);
shell_exec("convert -delay 20 -loop 0 $files_str $fileName");
echo $fileName;
?>
