<?php

use Symfony\Component\Console\Application;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Commands\ImportStationsCommand;
use Grimarina\CityBike\Commands\ImportTripsCommand;

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

$application->add(new ImportStationsCommand($stationsRepository));
$application->add(new ImportTripsCommand($tripsRepository));

$application->run();
