<?php
$fileNames = array();
$year = date('Y', strtotime('yesterday'));
$month = date('m', strtotime('yesterday'));

chdir(getcwd().'/daily_images/');
foreach (glob("plantbot-$year*$month-12*.jpg") as $filename) {
    $files[] = $filename;
}
$fileName = "monthgif-$year-$month-".date('H-s').".gif";
$files_str = implode(' ', $files);
shell_exec("convert -delay 20 -loop 0 $files_str $fileName");
echo $fileName;
?>
