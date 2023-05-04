<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Actions\Stations\ImportStations;
use Grimarina\CityBike\http\{Request, ErrorResponse};


class ImportStationsTest extends TestCase
{
    public function testImportStationsWithInvalidExtension()
    {
        $filename = 'stations.txt';
        $stationsRepository = $this->createMock(StationsRepository::class);
        $mockRequest = $this->createMock(Request::class);
        $action = new ImportStations($filename, $stationsRepository);

        $response = $action->handle($mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Invalid file extension. Only CSV files are allowed.', $response->payload()['reason']);
    }
}
