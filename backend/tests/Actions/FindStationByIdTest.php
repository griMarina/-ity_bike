<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Entities\Station;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\http\{Request, SuccessfulResponse, ErrorResponse};
use Grimarina\CityBike\Actions\Stations\FindStationById;
use Grimarina\CityBike\Exceptions\StationNotFoundException;

class FindStationByIdTest extends TestCase
{
    private $stationsRepository;
    private $mockRequest;

    protected function setUp(): void
    {
        // Create a mock instance of StationsRepository and Request for testing
        $this->stationsRepository = $this->createMock(StationsRepository::class);
        $this->mockRequest = $this->createMock(Request::class);
    }

    public function testItReturnsSuccessfulResponseWithStatus200(): void
    {
        // Define the expected station's data
        $station = new Station(1, 'Test Station 1', '', '', 'Test Address 1', 'Test Address 1', '', '', '', 10, 60.123, 24.456);
        $info = [
            'total_start' => 100,
            'total_end' => 200,
            'avg_distance_start' => 1000,
            'avg_distance_end' => 2000
        ];

        $expectedRestult = [
            'id' => 1,
            'name' => 'Test Station 1',
            'address' => 'Test Address 1',
            'capacity' => 10,
            'x' => 60.123,
            'y' => 24.456,
            'total_start' => 100,
            'total_end' => 200,
            'avg_distance_start' => 1000,
            'avg_distance_end' => 2000
        ];

        // Set up the expectations for the mock methods
        $this->stationsRepository
            ->expects($this->once())
            ->method('getMoreInfoById')
            ->willReturn($info);

        $this->stationsRepository
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($station);

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('1');

        $action = new FindStationById($this->stationsRepository);

        // Execute the action and get the response
        $response = $action->handle($this->mockRequest);

        // Assert the response type, status, and payload values
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($expectedRestult, $response->payload()['data']);
    }

    public function testItReturnsErrorResponseWithStatus404IfStationNotFound(): void
    {
        // Set up the expectations for the mock methods
        $this->stationsRepository
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new StationNotFoundException('Cannot find station: 1.'));

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('1');

        $action = new FindStationById($this->stationsRepository);

        // Execute the action and get the response
        $response = $action->handle($this->mockRequest);

        // Assert the response type, status, and payload values
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(404, $response->status());
        $this->assertEquals('Cannot find station: 1.', $response->payload()['reason']);
    }

    public function testItReturnsErrorResponseWithStatus400IfStationIdInvalid(): void
    {
        // Set up the expectations for the mock methods
        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('invalid_id');

        $action = new FindStationById($this->stationsRepository);

        // Execute the action and get the response
        $response = $action->handle($this->mockRequest);

        // Assert the response type, status, and payload values
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Invalid station id.', $response->payload()['reason']);
    }
}
