<?php

if (!(is_file('settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file('settings.txt');

include_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();

if (!is_file($ini['google_auth_file_location'])) {
    die("Wrong google_auth_file_location in settings.txt or missing google auth json file on path ".$ini['google_auth_file_location']);
}

putenv('GOOGLE_APPLICATION_CREDENTIALS='.$ini['google_auth_file_location']);
$client->useApplicationDefaultCredentials();

$client->setApplicationName("Plantbot");
$client->setScopes(['https://www.googleapis.com/auth/prediction']);
$service = new Google_Service_Prediction($client);

$project = $ini['prediction_project_name'];
//$project = 'sacred-armor-142709';

if (false) {
    $model['id'] = 'harry';
    echo "Killing model ".$model['id']."\n";
    $service->trainedmodels->delete($project, $model['id']);
    
    echo "Training model..\n";
    $insert = new Google_Service_Prediction_Insert($client);
    $insert->setId('harry');
    $insert->setModelType("REGRESSION");
    $ti = new Google_Service_Prediction_InsertTrainingInstances($client);

    $insert->setTrainingInstances($ti);
    $insert->setStorageDataLocation('');
    $mupp = $service->trainedmodels->insert($project, $insert);
    //dahbug::dump($mupp);
}

$models = $service->trainedmodels->listTrainedmodels($project);
if (count($models['modelData']) > 0){
    foreach($models['modelData']['items'] as $model){

        if ($model['id'] != 'harry') {
            echo "Killing non-harry model ".$model['id']."\n";
            $service->trainedmodels->delete($project, $model['id']);
        }

        if ($model['id'] == 'harry') {
            //dahbug::dump($model);
            $status = $service->trainedmodels->get($project, $model['id']);
            //dahbug::dump($status);
            echo "Status: ".$status->getTrainingStatus()."\n";
            echo "Instances: ".$status->getModelInfo()->numberInstances."\n";

            if ($status->getTrainingStatus() == 'RUNNING') {
                die("Training is RUNNING, aborting.\n");
            }

            if ($status->getTrainingStatus() == 'DONE' && $status->getModelInfo()->numberInstances > 0) {
                echo "Ok.. trying to predict then..\n";

                $predictionData = new Google_Service_Prediction_InputInput($client);
                $predictionData->setCsvInstance(array('22,33,44,55')); 
                $input = new Google_Service_Prediction_Input();
                $input->setInput($predictionData);
                try {
                    $result = $service->trainedmodels->predict($project, $model['id'], $input);
                    dahbug::dump($result);
                    //die;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

            if ($status->getTrainingStatus() == 'DONE') {
                echo "Updating..\n";
                $update = new Google_Service_Prediction_Update();
                $data = getRandomData(1);
                echo "Setting data: $data\n";
                $update->setCsvInstance(array($data));
                $resultbs = '';
                $update->setOutput($resultbs);
                $predictionService = new Google_Service_Prediction($client);
                $predictionModel = $predictionService->trainedmodels;

                //$model = $update->trainedModels;
                $updateresult = $predictionModel->update($project, 'harry' , $update);
                dahbug::dump($updateresult);
                dahbug::dump($resultbs);

            }
            
            if (false) {
                echo "Analyzing model..\n";
                $analyze = $service->trainedmodels->analyze($project, $model['id']);
                dahbug::dump($analyze);
                dahbug::dump($analyze->getDataDescription());
                //dahbug::dump($analyze->getDataDescription()->getFeatures());
            }
        }
    }
}

function getRandomData($count = 5) {
    $arr = array();
    for ($i=1; $i<=$count; $i++) {
        $arr1 = array();
        for ($j=1; $j<=5; $j++) {
            $arr1[] = rand(10,20);
        }
        $arr[] = $arr1;
    }

    $str = '';
    foreach ($arr as $a) {
        $str .= '"'.implode(',', $a).'",';
    }
    $str = substr($str, 0, -1);
    return $str;
}
