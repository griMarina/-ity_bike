<?php

use Symfony\Component\Console\Application;
use Grimarina\CityBike\Repositories\{StationsRepository, TripsRepository};
use Grimarina\CityBike\Commands\{ImportStationsCommand, ImportTripsCommand};

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
} catch (PDOException $e) {
    throw new RuntimeException('Failed to connect to database: ' . $e->getMessage());
}

$stationsRepository = new StationsRepository($pdo);
$tripsRepository = new TripsRepository($pdo);

$application = new Application();

// Add the command for stations import to the console application
$application->add(new ImportStationsCommand($stationsRepository));

// Add the command for trips import to the console application
$application->add(new ImportTripsCommand($tripsRepository));

// Run the console application
$application->run();
