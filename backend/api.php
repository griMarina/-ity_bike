<?php

use Grimarina\CityBike\http\{Request, ErrorResponse};
use Grimarina\CityBike\Exceptions\HttpException;
use Grimarina\CityBike\Actions\Stations\{ImportStations, FindAllStations, FindStationById};
use Grimarina\CityBike\Actions\Trips\{ImportTrips, FindAllTrips};
use Grimarina\CityBike\Repositories\{StationsRepository, TripsRepository};

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'),);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
} catch (PDOException $e) {
    throw new RuntimeException('Failed to connect to database: ' . $e->getMessage());
}

$routes = [
    'GET' => [
        '/stations/show' => new FindAllStations(new StationsRepository($pdo)),
        '/station/show' => new FindStationById(new StationsRepository($pdo)),
        '/trips/show' => new FindAllTrips(new TripsRepository($pdo)),
    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
