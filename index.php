<?php

use Grimarina\CityBike\http\Request;
use Grimarina\CityBike\http\ErrorResponse;
use Grimarina\CityBike\Exceptions\HttpException;
use Grimarina\CityBike\http\Actions\ImportStations;
use Grimarina\CityBike\Repositories\StationsRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hel_city_bike;charset=utf8', 'admin', 'asennus');
} catch (PDOException $e) {
    throw new RuntimeException('Failed to connect to database: ' . $e->getMessage());
}

$routes = [
    '/stations/import' => new ImportStations(__DIR__ . '/data/stations.csv', new StationsRepository($pdo))
];

if (!array_key_exists($path, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$path];

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
