<?php

require_once __DIR__ . '/vendor/autoload.php';

use Grimarina\CityBike\Entities\Station;

$station = new Station(501, 'Hanasaari', 'Hanaholmen', 'Hanasaari', 'Hanasaarenranta 1', 'Hanaholmsstranden 1', 'Espoo', 'Esbo', 'CityBike Finland', 10, 24.840319, 60.16582);

print $station;
