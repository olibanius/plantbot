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
            $plants[] = $row;
        }
        return $plants;
    }

    function saveData($data) {
        print_r($data);
    }

    function updateLastFeedTime($data) {
        $query = "UPDATE plants set last_feed_time='".$data['last_feed_time']."' where id=".$data['id'];
        $result = $this->queryDb($query);

    }

    function createPlant($name, $nickname, $location, $kind, $feedIntervalAprox, $soilTreshold, $feedVolume, $feedMode) {
        $query = "INSERT INTO plants set name='$name', nickname='$nickname', location='$location', kind='$kind', feed_interval_aprox=$feedIntervalAprox, soil_treshold=$soilTreshold, feed_volume=$feedVolume, feed_mode='$feedMode', birthday=NOW(), last_feed_time=NOW(), active=1;";
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
