<?php

namespace Repositories;

use PHPUnit\Framework\TestCase;
use League\Csv\Reader;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\{InvalidArgumentException, TripNotFoundException};
use Grimarina\CityBike\Entities\Trip;

class TripsRepositoryTest extends TestCase
{
    private $statementMock;
    private $connectionStub;
    private $tripsRepository;

    public function setUp(): void
    {
        $this->connectionStub = $this->createStub(\PDO::class);
        $this->statementMock = $this->createMock(\PDOStatement::class);
        $this->tripsRepository = new TripsRepository($this->connectionStub);
    }

    public function testImportCsvInsertsCorrectData(): void
    {
        // Create a CSV file with correct data
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100']
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        // Load the CSV file into a League\Csv\Reader object
        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Set up expectations for the mock PDOStatement object
        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with(
                [
                    ':departure' => '2021-05-01T00:00:11',
                    ':return' => '2021-05-01T00:04:34',
                    ':departure_station_id' => '1',
                    ':departure_station_name' => 'Station A',
                    ':return_station_id' => '2',
                    ':return_station_name' => 'Station B',
                    ':distance' => 1000,
                    ':duration' => 100,
                ],
            );
        $this->connectionStub->method('prepare')->willReturn($this->statementMock);

        // Call the importCsv method with the Reader object
        $this->tripsRepository->importCsv($csv);
    }

    public function testTripDurationIsLessThanTenSec(): void
    {
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'],
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '5', 'Station E', '6', 'Station F', '500', '0'],
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Create a reflection to make the private method validateCsv() accessible and check the ResultSet
        $reflection = new \ReflectionClass($this->tripsRepository);
        $method = $reflection->getMethod('validateCsv');
        $method->setAccessible(true);

        $resultSet = $method->invoke($this->tripsRepository, $csv);

        $this->assertCount(1, $resultSet);
    }

    public function testTripDistanceIsLessThanTenMetres(): void
    {
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'],
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '5', 'Station E', '6', 'Station F', '0', '500'],
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Create a reflection to make the private method validateCsv() accessible and check the ResultSet
        $reflection = new \ReflectionClass($this->tripsRepository);
        $method = $reflection->getMethod('validateCsv');
        $method->setAccessible(true);

        $resultSet = $method->invoke($this->tripsRepository, $csv);

        $this->assertCount(1, $resultSet);
    }

    public function testArrivalHappensBeforeDeparture(): void
    {
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'],
            ['2021-05-01T00:00:33', '2020-05-01T00:06:00', '5', 'Station E', '6', 'Station F', '1000', '100'],
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Create a reflection to make the private method validateCsv() accessible and check the ResultSet
        $reflection = new \ReflectionClass($this->tripsRepository);
        $method = $reflection->getMethod('validateCsv');
        $method->setAccessible(true);

        $resultSet = $method->invoke($this->tripsRepository, $csv);

        $this->assertCount(1, $resultSet);
    }

    public function testInvalidIntegers(): void
    {
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'], // valid row
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '3_id', 'Station E', '4', 'Station F', '1000', '100'], // invalid departure station id
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '5', 'Station C', '-6', 'Station D', '1000', '100'], // invalid return station id
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '7', 'Station E', '8', 'Station F', '-100m', '100'], // invalid distance
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '9', 'Station E', '10', 'Station F', '100', '100sec'], // invalid duration
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Create a reflection to make the private method validateCsv() accessible and check the ResultSet
        $reflection = new \ReflectionClass($this->tripsRepository);
        $method = $reflection->getMethod('validateCsv');
        $method->setAccessible(true);

        $resultSet = $method->invoke($this->tripsRepository, $csv);

        $this->assertCount(1, $resultSet);
    }

    public function testArrivalAndDepartureAreNotParseable(): void
    {
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'], 
            ['invalid_departure_data', '2021-05-01T00:06:00', '9', 'Station E', '10', 'Station F', '100', '100'],
            ['2021-05-01T00:00:33', 'invalid_return_data', '9', 'Station E', '10', 'Station F', '100', '100'],
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Create a reflection to make the private method validateCsv() accessible and check the ResultSet
        $reflection = new \ReflectionClass($this->tripsRepository);
        $method = $reflection->getMethod('validateCsv');
        $method->setAccessible(true);

        $resultSet = $method->invoke($this->tripsRepository, $csv);

        $this->assertCount(1, $resultSet);
    }

    public function testGetAllReturnsExpectedData(): void
    {
        $expected = [
            [
                'id' => 1,
                'departure' => '2021-05-01T00:00:11',
                'return' => '2021-05-01T00:04:34',
                'departure_station_id' => '1',
                'departure_station_name' => 'Station A',
                'return_station_id' => '2',
                'return_station_name' => 'Station B',
                'distance' => 1000,
                'duration' => 100,
            ],
            [
                'id' => 2,
                'departure' => '2021-05-01T00:00:11',
                'return' => '2021-05-01T00:04:34',
                'departure_station_id' => '2',
                'departure_station_name' => 'Station B',
                'return_station_id' => '2',
                'return_station_name' => 'Station A',
                'distance' => 2000,
                'duration' => 200,
            ],
        ];

        $this->statementMock->expects($this->atLeastOnce())
            ->method('bindValue')
            ->withConsecutive(
                [$this->equalTo(':offset'), $this->equalTo(0), $this->equalTo(\PDO::PARAM_INT)],
                [$this->equalTo(':limit'), $this->equalTo(20), $this->equalTo(\PDO::PARAM_INT)]
            );

        $this->statementMock->expects($this->once())
            ->method('execute');

        $this->statementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with($this->equalTo(\PDO::FETCH_ASSOC))
            ->willReturn($expected);

        $this->connectionStub
            ->expects($this->once())
            ->method('prepare')
            ->with("SELECT id, departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration FROM `trips` LIMIT :offset, :limit;")
            ->willReturn($this->statementMock);

        foreach ($expected as &$row) {
            $row['distance'] = round(($row['distance'] / 1000), 2);
            $row['duration'] = $row['duration'] / 60;
        }

        $result = $this->tripsRepository->getAll(1, 20);

        $this->assertEquals($expected, $result);
    }

    public function testGetByIdReturnsTripObjectIfFound(): void
    {
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        $this->statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Trip::class)
            ->willReturn([new Trip()]);

        $this->connectionStub->expects($this->once())
            ->method('prepare')
            ->with("SELECT id, departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration FROM `trips` WHERE trips.id = :id")
            ->willReturn($this->statementMock);

        $result = $this->tripsRepository->getById(1);

        $this->assertInstanceOf(Trip::class, $result);
    }

    public function testGetByIdThrowsExceptionIfTripNotFound(): void
    {
        $this->statementMock->method('execute')->willReturn(null);
        $this->statementMock->method('fetchAll')->willReturn([]);

        $this->connectionStub->method('prepare')->willReturn($this->statementMock);

        $this->expectException(TripNotFoundException::class);
        $this->expectExceptionMessage('Cannot find trip: 1');

        $this->tripsRepository->getById(1);
    }
}
