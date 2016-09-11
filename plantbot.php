<?php
ini_set("log_errors", 1);
ini_set("error_log", "plantbot.log");

require_once __DIR__ . '/www/OpenWeatherMap-PHP-Api/Examples/bootstrap.php';
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

//Todo: DB-structure and setup routine
$query = "DROP DATABASE IF EXISTS plantbot;";
$query = "CREATE DATABASE plantbot;";
$query = "CREATE TABLE plants ( id int autoincrement,
                                name varchar(52),
                                ...
                                primary key (id)";
$query = "CREATE TABLE data (   id int autoincrement,
                                plant_id int,
                                ...
                                primary key(id)";

$lang = 'se';
$units = 'metric';

$plantData = array( 'name' => 'Plant 1',
                    'nickname' => 'Janne',
                    'birthday' => '2016-09-09',
                    'location' => 'Indoors',
                    'kind' => 'Fredskalla',
                    'feed_interval_aprox' => 3,
                    'soil_treshold' => 83,
                    'feed_volume' => 100,
                    'feed_mode' => 'soilTemp',
                    'last_feed_time' => '2016-09-11 18:00',
                    'img_url' => '',
                  );

print_r($plantData);

$data = array();

$owm = new OpenWeatherMap();
$owm->setApiKey($myApiKey);
$weather = $owm->getWeather('Mölndal', $units, $lang);
$clouds = $weather->clouds->getValue();

$data['soil'] = (int)getMoistLevel();
$data['name'] = $plantData['name'];
$data['time'] = date('Y-m-d H:i');
$data['temp'] = (int)getTemperature();
$data['humidity'] = (int)getHumidity();
$data['pressure'] = (int)getPressure();
$data['day_of_year'] = date('z')+1;
$data['clouds'] = $clouds;
$data['age_days'] = floor((time() - strtotime($plantData['birthday'])) / (60 * 60 * 24));

if ($plantData['location'] == 'Indoors') {
    if ($data['soil'] <= $plantData['soil_treshold']) {
        // Todo: Ska matning bara tillåtas vissa tider?
        // Todo: Vattna
        error_log("Vattnar ".$plantData['nickname']." med ".$plantData['feed_volume']." cl vatten.");
        $data['time_since_last_feeding_hours'] = 0;
    } else {
        $data['time_since_last_feeding_hours'] = floor((time() - strtotime($plantData['last_feed_time'])) / (60 * 60));
    }
} else {
    /* Outdoor Weather stuffs
    echo $temp = $weather->temperature->getValue();
    echo $humidity = $weather->humidity->getValue();
    echo $pressure = $weather->pressure->getValue();
    echo $weather->wind->speed->getValue();
    echo $weather->wind->direction->getValue();
    echo $weather->wind->direction->getUnit();
    echo $weather->wind->direction->getDescription();
    echo $weather->precipitation->getValue();
    echo $weather->precipitation->getDescription();
    */
}

print_r($data);
//Todo: Spara data till db
//Todo: Update plantdata
//Todo: Pusha data till predictionmodellen
//Todo: Ta en bild per dag
//Todo: Statuspage/Dashboard eller mail?


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
