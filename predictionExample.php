
<?php
set_include_path("/var/www/html/bb-dev/lib/google-api-php-client/src/");
require_once 'Google/Client.php';
require_once 'Google/Service/Prediction.php';

$client_id = 'ddddddd.apps.googleusercontent.com'; //Client ID
$service_account_name = 'dd...@developer.gserviceaccount.com'; //Email Address 
$key_file_location = '/var/www/html/bb-dev/protected/commands/google/config/ddddd.p12'; //key.p12
$project = 'sanguine-signal-693';

$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
$service = new Google_Service_Prediction($client);

$key = file_get_contents($key_file_location);
$cred = new Google_Auth_AssertionCredentials(
    $service_account_name,
    array('https://www.googleapis.com/auth/devstorage.full_control',
                    'https://www.googleapis.com/auth/devstorage.read_only',
                    'https://www.googleapis.com/auth/devstorage.read_write',
                    'https://www.googleapis.com/auth/prediction'   
                    ),
    $key
);
$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {
  $client->getAuth()->refreshTokenWithAssertion($cred);
}


// Delete
$models = $service->trainedmodels->listTrainedmodels($project);
if (count($models['modelData']) > 0){
    foreach($models['modelData']['items'] as $model){
        if ($model['id'] = 'languageidentifier'){
            // Delete Model
            $service->trainedmodels->delete($project, 'languageidentifier');
        }
    }
}
exit;


// Training Model
$insert = new Google_Service_Prediction_Insert($client);
$insert->setId('languageidentifier');
$insert->setStorageDataLocation('test-tn/language_id.txt'); // A file in Cloud Storage
$service->trainedmodels->insert($project, $insert);
exit;

// Get Prediction
$predictionText = "rematado";
$predictionData = new Google_Service_Prediction_InputInput();
$predictionData->setCsvInstance(array($predictionText));

$input = new Google_Service_Prediction_Input();
$input->setInput($predictionData);
$hostedmodels = $service->trainedmodels->predict($project, 'languageidentifier', $input);
print '<h2>Prediction Result:</h2><pre>' . print_r($hostedmodels, true) . '</pre>';
exit;


