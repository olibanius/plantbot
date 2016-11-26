<?php
ini_set("log_errors", 1);
ini_set("error_log", "plantbot.log");

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

require 'vendor/autoload.php';

if (!(is_file('settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file('settings.txt');

include('db-model.php');
$db = new Db_model;

$owm = new OpenWeatherMap();
$owm->setApiKey($ini['ow_api_key']);
try {
    $weather = $owm->getWeather($ini['ow_city'], $ini['ow_units'], $ini['ow_lang']);
    $clouds = $weather->clouds->getValue();
    $pressure = $weather->pressure->getValue();
} catch (Exception $e) {
    error_log("Error contacting to Weather API");
    $clouds = 0;
    $pressure = 0;
}
$plants = $db->getPlants();
//echo "-- PLANTS --\n";
//print_r($plants);

foreach ($plants as $plantData) {
    $data = array();
    $data['plant_id'] = $plantData['id'];
    $data['soil'] = (int)getMoistLevel($plantData['moist_sensor_nr']);
    $data['time'] = date('Y-m-d H:i:s');
    $data['temp'] = (int)getTemperature();
    $data['humidity'] = (int)getHumidity();
    $data['pressure'] = $pressure;
    $data['day_of_year'] = date('z')+1;
    $data['clouds'] = $clouds;
    $data['age_days'] = floor((time() - strtotime($plantData['birthday'])) / (60 * 60 * 24));
    
    if ($plantData['location'] == 'Outdoors') {
        // More sensors and data?
        echo $temp = $weather->temperature->getValue();
        echo $humidity = $weather->humidity->getValue();
        echo $weather->wind->speed->getValue();
        echo $weather->wind->direction->getValue();
        echo $weather->wind->direction->getUnit();
        echo $weather->wind->direction->getDescription();
        echo $weather->precipitation->getValue();
        echo $weather->precipitation->getDescription();
    }

    if ($plantData['location'] == 'Indoors') {
        if ($data['soil'] <= $plantData['soil_treshold']) {
            $watered = waterPlant($plantData);
            $data['time_since_last_feeding_hours'] = 0;
            $plantData['last_feed_time'] = date('Y-m-d H:i');
            $db->updateLastFeedTime($plantData);
            postTextToSlack($plant['nickname']. " matades med ".$plant['feed_volume']." cl vatten.");
        } else {
            $data['time_since_last_feeding_hours'] = floor((time() - strtotime($plantData['last_feed_time'])) / (60 * 60));
        }
    } else {
        /* Outdoor Weather stuffs
        */
    }

    $db->saveData($data);
    //print_r($plantData);
}

//Todo: Pusha data till predictionmodellen
//Todo: Ta en bild per dag
//Todo: Posta bilden till en Slack-kanal
//Todo: Statuspage/Dashboard eller mail?
//Todo: Bevaka low-water-supply?
//Todo: Flasha lampa i lägenheten?

function postTextToSlack($text) {
    shell_exec("php /home/pi/plantbot/postToSlack.php '$text'");
}

function waterPlant($plantData) {
    // Todo: Ska matning bara tillåtas vissa tider?
    
    // Todo: Vattna !!!

    $feedTime = $plantData['feed_volume']/100;
    $motorGPIO = $plantData['motor_GPIO'];

    $ok = trim(shell_exec("python waterPlant.py $motorGPIO $feedTime"));
    if (!true) {
        die('damnit error happened');
    } else {
        error_log("Vattnade ".$plantData['nickname']." med ".$plantData['feed_volume']." cl vatten.");
    }
    return $ok;
}

function getMoistLevel($sensorNr) {
    return shell_exec("python moistLevel.py $sensorNr");
}

function getTemperature() {
    return shell_exec('python temperature.py');
}

function getHumidity() {
    return shell_exec('python humidity.py');
}
