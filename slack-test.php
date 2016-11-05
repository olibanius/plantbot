<?php
$json = '{
    "attachments": [
        {
            "image_url": "https://dl.dropboxusercontent.com/u/2839752/Wedding.gif",
            "image_url": "https://dl.dropboxusercontent.com/u/2839752/2018-08-07%2008.32.34.jpg",
            "text": "Here be test image"
        }
    ]
}';
$json = str_replace('"', '\"', $json);

$uri = 'https://hooks.slack.com/services/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';

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
