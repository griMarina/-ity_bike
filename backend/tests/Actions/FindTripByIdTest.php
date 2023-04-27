<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Entities\Trip;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\http\{Request, SuccessfulResponse, ErrorResponse};
use Grimarina\CityBike\Actions\Trips\FindTripById;
use Grimarina\CityBike\Exceptions\TripNotFoundException;

class FindTripByIdTest extends TestCase
{
    private $tripsRepository;
    private $mockRequest;

    protected function setUp(): void
    {
        $this->tripsRepository = $this->createMock(TripsRepository::class);
        $this->mockRequest = $this->createMock(Request::class);
    }

    public function testItReturnsSuccessfulResponse(): void
    {
        $station = new Trip(1, '2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', 1000, 100);

        $this->tripsRepository
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($station);

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('1');

        $action = new FindTripById($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $expectedRestult = [
            'id' => 1,
            'departure' => '2021-05-01T00:00:11',
            'return' => '2021-05-01T00:04:34',
            'departure_station_id' => '1',
            'departure_station_name' => 'Station A',
            'return_station_id' => '2',
            'return_station_name' => 'Station B',
            'distance' => 1000,
            'duration' => 100,
        ];

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals($expectedRestult, $response->payload()['data']);
    }

    public function testItReturnsErrorResponseIfTripNotFound(): void
    {
        $this->tripsRepository
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new TripNotFoundException('Cannot find trip: 1'));

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('1');

        $action = new FindTripById($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Cannot find trip: 1', $response->payload()['reason']);
    }
}
