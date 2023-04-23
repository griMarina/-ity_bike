<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Entities\Station;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\http\{Request, SuccessfulResponse, ErrorResponse};
use Grimarina\CityBike\Actions\Stations\FindById;
use Grimarina\CityBike\Exceptions\StationNotFoundException;

class FindStationByIdTest extends TestCase
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
        $station = new Station(1, 'Test Station 1', '', '', 'Test Address 1', 'Test Address 1', '', '', '', 10, 60.123, 24.456, 100, 100);

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

        $action = new FindById($this->stationsRepository);
        $response = $action->handle($this->mockRequest);

        $expectedRestult = [
            'id' => 1,
            'name' => 'Test Station 1',
            'address' => 'Test Address 1',
            'capacity' => 10,
            'x' => 60.123,
            'y' => 24.456,
            'start_trips' => 100,
            'end_trips' => 100,
        ];

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertEquals($expectedRestult, $response->payload()['data']);
    }

    public function testItReturnsErrorResponseIfStationNotFound(): void
    {
        $this->stationsRepository
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new StationNotFoundException('Cannot find station: 1'));

        $this->mockRequest
            ->expects($this->once())
            ->method('query')
            ->with('id')
            ->willReturn('1');

        $action = new FindById($this->stationsRepository);
        $response = $action->handle($this->mockRequest);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Cannot find station: 1', $response->payload()['reason']);
    }
}
