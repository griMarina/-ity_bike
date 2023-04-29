<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;
use Grimarina\CityBike\Actions\Stations\ImportStations;

class ImportStationsTest extends TestCase
{
    public function testImportStationsWithInvalidExtension()
    {
        $filename = 'stations.txt';
        $stationsRepository = $this->createMock(StationsRepository::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file extension. Only CSV files are allowed.');

        new ImportStations($filename, $stationsRepository);
    }
}
