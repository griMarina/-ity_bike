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

    public function testItReturnsSuccessfulResponseWithStatus200(): void
    {
        $trips = [
            [1, '2021-05-01T00:00:11', '2021-05-01T00:04:34', '2', 'Station A', '1', 'Station B', 1000, 100],
            [2, '2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station B', '2', 'Station A', 2000, 200],
        ];

        $this->tripsRepository
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn(2);

        $this->tripsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1, 10)
            ->willReturn($trips);

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', '1'],
                ['limit', '10'],
            ]);

        $action = new FindAllTrips($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($trips, $response->payload()['data']['trips']);
        $this->assertEquals(2, $response->payload()['data']['entries']);
    }

    public function testItReturnsErrorResponseWithStatus404IfTripsNotFound(): void
    {
        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', '1'],
                ['limit', '10'],
            ]);

        $this->tripsRepository
            ->expects($this->once())
            ->method('getAll')
            ->with(1, 10)
            ->willThrowException(new TripNotFoundException('Trips not found.'));

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', '1'],
                ['limit', '10'],
            ]);

        $action = new FindAllTrips($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(404, $response->status());
        $this->assertEquals('Trips not found.', $response->payload()['reason']);
    }

    public function testItReturnsErrorResponseWithStatus400IfParamsInvalid(): void
    {
        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnMap([
                ['page', 'invalid_page'],
                ['limit', 'invalid_limit'],
            ]);

        $action = new FindAllTrips($this->tripsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Invalid parameters.', $response->payload()['reason']);
    }
}
