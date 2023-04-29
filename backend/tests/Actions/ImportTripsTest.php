<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;
use Grimarina\CityBike\Actions\Trips\ImportTrips;

class ImportTripsTest extends TestCase
{
    public function testImportStationsWithInvalidExtension()
    {
        $filename = 'trips.txt';
        $tripsRepository = $this->createMock(TripsRepository::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file extension. Only CSV files are allowed.');

        new ImportTrips($filename, $tripsRepository);
    }
}
