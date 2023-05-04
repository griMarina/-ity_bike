<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Actions\Trips\ImportTrips;
use Grimarina\CityBike\http\{Request, ErrorResponse};

class ImportTripsTest extends TestCase
{
    public function testImportStationsWithInvalidExtension()
    {
        $filename = 'trips.txt';
        $tripsRepository = $this->createMock(TripsRepository::class);
        $mockRequest = $this->createMock(Request::class);
        $action = new ImportTrips($filename, $tripsRepository);

        $response = $action->handle($mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Invalid file extension. Only CSV files are allowed.', $response->payload()['reason']);
    }
}
