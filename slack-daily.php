<?php
$imgUrl = $argv[1];
$json = '{
    "attachments": [
        {
            "image_url": "'.$imgUrl.'",
            "text": "Här är dagens bild!"
        }
    ]
}';
$json = str_replace('"', '\"', $json);

$settingsFile = "/home/pi/plantbot/settings.txt";
if (!(is_file($settingsFile))) die('settings.txt does not exist');
$ini = parse_ini_file($settingsFile);

$uri = $ini['slack_uri'];

try {
  ob_start();
  $curl = 'curl -X POST --data "payload='.$json.'" '.$uri;
  var_dump($curl);
  passthru($curl);
  $response = ob_get_contents();
  ob_end_clean();

  $retArr = json_decode($response, true);

  return $retArr;
} catch (Exception $e) {
  throw($e);
}
