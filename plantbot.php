<?php
ini_set("log_errors", 1);
ini_set("error_log", "plantbot.log");

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

require 'vendor/autoload.php';

$ini = parse_ini_file('settings.txt');

include('db-model.php');
$db = new Db_model;

$owm = new OpenWeatherMap();
$owm->setApiKey($ini['ow_api_key']);
$weather = $owm->getWeather($ini['ow_city'], $ini['ow_units'], $ini['ow_lang']);
$clouds = $weather->clouds->getValue();

$plants = $db->getPlants();
foreach ($plants as $plantData) {
    $data = array();
    $data['plant_id'] = $plantData['id'];
    $data['soil'] = (int)getMoistLevel();
    $data['time'] = date('Y-m-d H:i');
    $data['temp'] = (int)getTemperature();
    $data['humidity'] = (int)getHumidity();
    $data['pressure'] = (int)getPressure(); // Todo: Saknar sensor för detta va?
    $data['day_of_year'] = date('z')+1;
    $data['clouds'] = $clouds;
    $data['age_days'] = floor((time() - strtotime($plantData['birthday'])) / (60 * 60 * 24));
    
    if ($plantData['location'] == 'Outdoors') {
        // More sensors and data?
        echo $temp = $weather->temperature->getValue();
        echo $humidity = $weather->humidity->getValue();
        echo $pressure = $weather->pressure->getValue();
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
        } else {
            $data['time_since_last_feeding_hours'] = floor((time() - strtotime($plantData['last_feed_time'])) / (60 * 60));
        }
    } else {
        /* Outdoor Weather stuffs
        */
    }

    //Todo: Spara data till db
    $db->saveData($data);
}
echo "-- PLANTS --\n";
print_r($plants);

//Todo: Update plantdata
//Todo: Pusha data till predictionmodellen
//Todo: Ta en bild per dag
//Todo: Statuspage/Dashboard eller mail?
//Todo: Bevaka low-water-supply?
//Todo: Flasha lampa i lägenheten?

function waterPlant($plantData) {
    // Todo: Ska matning bara tillåtas vissa tider?
    
    // Todo: Vattna !!!
    $ok = trim(shell_exec('python waterPlant.py "'.$plantData['feed_volume'].'"'));
    if (!true) {
        die('damnit error happened');
    } else {
        error_log("Vattnade ".$plantData['nickname']." med ".$plantData['feed_volume']." cl vatten.");
    }
    return $ok;
}

function getMoistLevel() {
    return shell_exec('python moistLevel.py');
}

function getTemperature() {
    return shell_exec('python temperature.py');
}

function getHumidity() {
    return shell_exec('python humidity.py');
}

function getPressure() {
    return shell_exec('python pressure.py');
}
