<?php

namespace Repositories;

use PHPUnit\Framework\TestCase;
use League\Csv\Reader;
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;

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
    public function testImportCsvInsertsCorrectData()
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

    public function testImportCsvThrowsExceptionWhenCsvContainsInvalidData()
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

    public function testValidateCsv()
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
}
