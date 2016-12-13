<?php

ini_set("log_errors", 1);
ini_set("error_log", "plantbot.log");

class Db_model {

    function db_connect($forceDB = false) {
        static $connection;

        //if ($forceDB) echo "FORCING SELECT DB! (Probably due to re-deployed db)\n";

        if (!isset($connection) || $forceDB) {
            $ini = parse_ini_file('settings.txt');
            $connection = mysqli_connect($ini['db_server'], $ini['db_user'], $ini['db_password']);
            if ($connection === false) {
                die (mysqli_error());
            }
            mysqli_select_db($connection, $ini['db_name']);
        }
        return $connection;
    }

    function queryDb($query) {
        //error_log($query);
        $connection = $this->db_connect();
        $result = mysqli_query($connection, $query);
        if (!$result) {
            die(mysqli_error($connection));
        }
        return $result;
    }

    function getPlants() {
        $query = "SELECT * from plants where active=1";
        $result = $this->queryDb($query);
        $plants = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $plants[$row['id']] = $row;
        }
        return $plants;
    }

    function getPlantsData() {
        $plants = $this->getPlants();
        foreach ($plants as $plant) {
            $query = "SELECT * from plantdata where plant_id=".$plant['id']." order by time desc limit 1";
            $result = $this->queryDb($query);
            while ($row = mysqli_fetch_assoc($result)) {
                $plants[$plant['id']]['data'] = $row;
            }
        }
        return $plants;

    }

    function getFeedingStats($startdate, $enddate) {
        $plantdata = array();
        $query = "select plant_id, soil, time, temp, age_days from plantdata where time >= '$startdate 00:00' and time <= '$enddate 23:59' and time_since_last_feeding_hours = 0 order by time asc";
        $result = $this->queryDb($query);
        while ($row = mysqli_fetch_assoc($result)) {
            $plantdata[$row['plant_id']][] = $row;
        }
        return $plantdata;
    }

    function getShareLink($fileUri) {
        $query = "select share_link from shareLinks where file_uri='$fileUri'";
        $result = $this->queryDb($query);
        if (count($result) > 0 ) {
            $row = mysqli_fetch_assoc($result);
            return $row['share_link'];
        } else {
            shell_exec("php $workingDir/dropboxUploadFile.php $fileUri");
            $link = shell_exec("php $workingDir/dropboxShareLink.php $fileUri");
            $query = "insert into shareLinks set file_uri='$fileUri', share_link='$link';";
            $result = $this->queryDb($query);
            return $link;
        }

    }

    function saveData($data) {
        $query = "INSERT INTO plantdata set 
            plant_id = {$data['plant_id']}, 
            soil = {$data['soil']},
            time = '{$data['time']}',
            temp = {$data['temp']},
            humidity = {$data['humidity']},
            pressure = {$data['pressure']},
            day_of_year = {$data['day_of_year']},
            clouds = {$data['clouds']},
            age_days = {$data['age_days']},
            time_since_last_feeding_hours = {$data['time_since_last_feeding_hours']}
            ";
        $result = $this->queryDb($query);
    }

    function updateLastFeedTime($data) {
        $query = "UPDATE plants set last_feed_time='".$data['last_feed_time']."' where id=".$data['id'];
        $result = $this->queryDb($query);

    }

    function createPlant($name, $nickname, $location, $kind, $feedIntervalAprox, $soilTreshold, $feedVolume, $feedMode, $moistSensorNr, $motorGPIO) {
        $query = "INSERT INTO plants set name='$name', nickname='$nickname', location='$location', kind='$kind', feed_interval_aprox=$feedIntervalAprox, soil_treshold=$soilTreshold, feed_volume=$feedVolume, feed_mode='$feedMode', moist_sensor_nr = '$moistSensorNr', motor_GPIO = '$motorGPIO', birthday=NOW(), last_feed_time=NOW(), active=1;";
        $result = $this->queryDb($query);
    }

    function createDatabase() {
        $query = "DROP DATABASE IF EXISTS plantbot;";
        $result = $this->queryDb($query);

        $query = "CREATE DATABASE plantbot;";
        $result = $this->queryDb($query);
        
        $connection = $this->db_connect($forceDB = true);

        $query = "CREATE TABLE plants  ( 
                                            id int auto_increment,
                                            name varchar(52),
                                            nickname varchar(52),
                                            birthday date,
                                            location varchar(52),
                                            kind varchar(52),
                                            feed_interval_aprox int,
                                            soil_treshold int,
                                            feed_volume int,
                                            feed_mode varchar(52),
                                            moist_sensor_nr int,
                                            motor_GPIO int,
                                            last_feed_time datetime,
                                            active bool,
                                            primary key (id)
                                        );";
        $result = $this->queryDb($query);
        
        $query = "CREATE TABLE plantdata    (   
                                                id int auto_increment,
                                                plant_id int,
                                                soil int,
                                                time datetime,
                                                temp float,
                                                humidity float,
                                                pressure float,
                                                day_of_year int,
                                                clouds float,
                                                age_days int,
                                                time_since_last_feeding_hours int,
                                                primary key(id)
                                            );";
        $result = $this->queryDb($query);
        
        $query = "CREATE TABLE shareLinks    (   
                                                id int auto_increment,
                                                file_uri varchar(255),
                                                share_link varchar(255),
                                                primary key(id)
                                            );";
        $result = $this->queryDb($query);
    }

    function setup() {
        echo "Creating new plantbot db\n";
        $this->createDatabase();
        
        $feedIntervalAprox = 3; // Days?
        $soilTreshold = 83;
        $feedVolume = 300; // Centiliters? Per feeding time?
        $feedMode = 'soilTemp';
        echo "Creating plant exampledata\n";
        $this->createPlant('Plant 1', 'Janne', 'Indoors', 'Fredskalla', $feedIntervalAprox, $soilTreshold, $feedVolume, $feedMode);
    }
}
