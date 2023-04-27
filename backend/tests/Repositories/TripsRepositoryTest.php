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

    public function testImportCsvThrowsExceptionWhenCsvContainsInvalidData(): void
    {
        // Create a CSV file with invalid data
        $csvData = [
            ['Invalid'],
            ['Data']
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Expect an exception to be thrown when the importCsv() method is called
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File contains invalid data');

        $this->tripsRepository->importCsv($csv);
    }

    public function testValidateCsv(): void
    {
        // Create a CSV file with valid and invalid data
        $csvData = [
            ['Departure', 'Return', 'Departure station id', 'Departure station name', 'Return station id', 'Return station name', 'Covered distance (m)', 'Duration (sec.)'],
            ['2021-05-01T00:00:11', '2021-05-01T00:04:34', '1', 'Station A', '2', 'Station B', '1000', '100'],
            ['2021-05-01T00:00:22', '2021-05-01T00:05:55', '3', 'Station C', '4', 'Station D', '5', '10'],
            ['2021-05-01T00:00:33', '2021-05-01T00:06:00', '5', 'Station E', '6', 'Station F', '500', '5'],
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Call the validateCsv() method and check the ResultSet
        $resultSet = $this->tripsRepository->validateCsv($csv);

        // Verify that the ResultSet only contains the valid rows
        $expectedResult =
            [
                'Departure' => '2021-05-01T00:00:11',
                'Return' => '2021-05-01T00:04:34',
                'Departure station id' => '1',
                'Departure station name' => 'Station A',
                'Return station id' => '2',
                'Return station name' => 'Station B',
                'Covered distance (m)' => '1000',
                'Duration (sec.)' => '100',
            ];

        $this->assertEquals($expectedResult, $resultSet->fetchOne());
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


        $result = $this->tripsRepository->getAll(1);

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
