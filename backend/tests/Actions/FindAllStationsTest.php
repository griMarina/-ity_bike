<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\http\{Request, SuccessfulResponse, ErrorResponse};
use Grimarina\CityBike\Actions\Stations\FindAllStations;
use Grimarina\CityBike\Exceptions\StationNotFoundException;

class FindAllStationsTest extends TestCase
{
    private $stationsRepository;
    private $mockRequest;

    protected function setUp(): void
    {
        $this->stationsRepository = $this->createMock(StationsRepository::class);
        $this->mockRequest = $this->createMock(Request::class);
    }

    public function testItReturnsSuccessfulResponse(): void
    {
        $stations = [
            ['id' => 1, 'name_fi' => 'Test Station 1', 'address_fi' => 'Test Address 1', 'capacity' => 10, 'coordinate_x' => 60.123, 'coordinate_y' => 24.456],
            ['id' => 2, 'name_fi' => 'Test Station 2', 'address_fi' => 'Test Address 2', 'capacity' => 20, 'coordinate_x' => 60.456, 'coordinate_y' => 24.789]
        ];

        $this->stationsRepository
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn(2);

        $this->stationsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1, 10)
            ->willReturn($stations);

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', '1'],
                ['limit', '10'],
            ]);

        $action = new FindAllStations($this->stationsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals($stations, $response->payload()['data']['stations']);
        $this->assertEquals(2, $response->payload()['data']['entries']);
        $this->assertIsFloat($response->payload()['data']['stations'][0]['coordinate_x']);
        $this->assertIsFloat($response->payload()['data']['stations'][0]['coordinate_y']);
    }

    public function testItReturnsErrorResponseIfStationsNotFound(): void
    {
        $this->stationsRepository
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn(2);

        $this->stationsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1)
            ->willThrowException(new StationNotFoundException('Stations not found.'));

         $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', '1'],
                ['limit', '10'],
            ]);

        $action = new FindAllStations($this->stationsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Stations not found.', $response->payload()['reason']);
    }
}
