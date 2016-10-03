<?php

if (!(is_file('settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file('settings.txt');

include_once __DIR__ . '/vendor/autoload.php';
include_once "templates/base.php";

echo pageHeader("Service Account Access");

$client = new Google_Client();

putenv('GOOGLE_APPLICATION_CREDENTIALS='.$ini['google_auth_file_location']);
$client->useApplicationDefaultCredentials();


/*
$client->setApplicationName("Client_Library_Examples");
$client->setScopes(['https://www.googleapis.com/auth/books']);


// returns a Guzzle HTTP Client
$httpClient = $client->authorize();

// make an HTTP request

$apiKey = 'NOT_SET';

$response = $httpClient->get("https://www.googleapis.com/prediction/v1.6/projects/plantbot-145111/trainedmodels/list?key=$apiKey");
dahbug::dump($response);

die('death');
*/

$client->setApplicationName("Client_Library_Examples");
$client->setScopes(['https://www.googleapis.com/auth/prediction']);
$project = 'plantbot-145111';
$service = new Google_Service_Prediction($client);

$models = $service->trainedmodels->listTrainedmodels($project);
if (count($models['modelData']) > 0){
    foreach($models['modelData']['items'] as $model){
        print_r($model);
        dahbug::dump($model);
    }
}
die;
$results = $service->trainedmodels->listTrainedmodels($project);
dahbug::dump($results);
die('death');

$service = new Google_Service_Books($client);

/************************************************
  We're just going to make the same call as in the
  simple query as an example.
 ************************************************/
$optParams = array('filter' => 'free-ebooks');
$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
?>

<h3>Results Of Call:</h3>
<?php foreach ($results as $item): ?>
  <?= $item['volumeInfo']['title'] ?>
  <br />
<?php endforeach ?>

<?php pageFooter(__FILE__); ?>
