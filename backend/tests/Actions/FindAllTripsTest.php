<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\http\{Request, SuccessfulResponse, ErrorResponse};
use Grimarina\CityBike\Actions\Trips\FindAllTrips;
use Grimarina\CityBike\Exceptions\TripNotFoundException;

class FindAllTripsTest extends TestCase
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
        $trips = [
            [1, '2021-05-01T00:00:11', '2021-05-01T00:04:34', '2', 'Station A', '1', 'Station B', 1000, 100],
            [2, '2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station B', '2', 'Station A', 2000, 200],
        ];

        $this->tripsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1)
            ->willReturn($trips);

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('page')
            ->willReturn('1');

        $action = new FindAllTrips($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals($trips, $response->payload()['data']);
    }

    public function testItReturnsErrorResponseIfTripsNotFound(): void
    {
        $this->tripsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1)
            ->willThrowException(new TripNotFoundException('Trips not found.'));

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('page')
            ->willReturn('1');

        $action = new FindAllTrips($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Trips not found.', $response->payload()['reason']);
    }
}
