<?php

namespace Repositories;

use PHPUnit\Framework\TestCase;
use League\Csv\Reader;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\{InvalidArgumentException, StationNotFoundException};
use Grimarina\CityBike\Entities\Station;

class StationsRepositoryTest extends TestCase
{
    private $statementMock;
    private $connectionStub;
    private $stationRepository;

    public function setUp(): void
    {
        $this->connectionStub = $this->createStub(\PDO::class);
        $this->statementMock = $this->createMock(\PDOStatement::class);
        $this->stationRepository = new StationsRepository($this->connectionStub);
    }
    public function testImportCsvInsertsCorrectData(): void
    {

        // Create a temporary CSV file with correct data
        $csvData = [
            ['FID', 'ID', 'Nimi', 'Namn', 'Name', 'Osoite', 'Adress', 'Kaupunki', 'Stad', 'Operaattor', 'Kapasiteet', 'x', 'y'],
            ['1', '1', 'Test Station 1', 'Test Station 1', 'Test Station 1', 'Test Address 1', 'Test Address 1', 'Test City 1', 'Test City 1', 'Test Operator 1', 10, 10.0000, 20.0000]
        ];

        $csvFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $csvFile = fopen($csvFilePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);

        // Load temp file into a League\Csv\Reader object
        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        // Set up expectations for the mock PDOStatement object
        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':id' => 1,
                ':name_fi' => 'Test Station 1',
                ':name_sv' => 'Test Station 1',
                ':name_en' => 'Test Station 1',
                ':address_fi' => 'Test Address 1',
                ':address_sv' => 'Test Address 1',
                ':city_fi' => 'Test City 1',
                ':city_sv' => 'Test City 1',
                ':operator' => 'Test Operator 1',
                ':capacity' => 10,
                ':coordinate_x' => 10.0000,
                ':coordinate_y' => 20.0000,
            ]);

        $this->connectionStub->method('prepare')->willReturn($this->statementMock);

        // Call the importCsv method with the Reader object
        $this->stationRepository->importCsv($csv);
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
        $this->expectExceptionMessage('File contains invalid data.');

        $this->stationRepository->importCsv($csv);
    }

    public function testGetAllReturnsExpectedData(): void
    {
        $expected = [
            [
                'id' => 1,
                'name_fi' => 'Station A',
                'address_fi' => 'Address A',
                'capacity' => 10,
                'coordinate_x' => 60.123456,
                'coordinate_y' => 24.123456,
            ],
            [
                'id' => 2,
                'name_fi' => 'Station B',
                'address_fi' => 'Address B',
                'capacity' => 20,
                'coordinate_x' => 60.654321,
                'coordinate_y' => 24.654321,
            ],
        ];

        $this->statementMock->expects($this->atLeastOnce())
            ->method('bindValue')
            ->withConsecutive(
                [$this->equalTo(':offset'), $this->equalTo(0), $this->equalTo(\PDO::PARAM_INT)],
                [$this->equalTo(':limit'), $this->equalTo(10), $this->equalTo(\PDO::PARAM_INT)]
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
            ->with("SELECT id, name_fi as `name`, address_fi as `address`, capacity, coordinate_x, coordinate_y FROM `stations` 
        ORDER BY id ASC LIMIT :offset, :limit ;")
            ->willReturn($this->statementMock);

        $page = 1;
        $limit = 10;
        $result = $this->stationRepository->getAll($page, $limit);

        $this->assertEquals($expected, $result);
    }

    public function testGetByIdReturnsStationObjectIfFound(): void
    {
        // Define the return value of the mock PDOStatement's execute method
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        // Define the return value of the mock PDOStatement's fetchAll method
        $this->statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Station::class)
            ->willReturn([new Station()]);

        // Configure the mock PDO object to return the mock PDOStatement object when prepare is called
        $this->connectionStub->expects($this->once())
            ->method('prepare')
            ->with("SELECT stations.id, stations.name_fi, stations.address_fi, stations.capacity, stations.coordinate_x, stations.coordinate_y
        FROM stations
        WHERE stations.id = :id
        GROUP BY stations.id, stations.name_fi, stations.address_fi, stations.capacity, stations.coordinate_x, stations.coordinate_y;")
            ->willReturn($this->statementMock);

        // Call the getById method with an ID of 1
        $result = $this->stationRepository->getById(1);

        // Assert that the result is an instance of Station
        $this->assertInstanceOf(Station::class, $result);
    }

    public function testGetByIdThrowsExceptionIfStationNotFound(): void
    {
        $this->statementMock->method('execute')->willReturn(null);
        $this->statementMock->method('fetchAll')->willReturn([]);

        $this->connectionStub->method('prepare')->willReturn($this->statementMock);

        $this->expectException(StationNotFoundException::class);
        $this->expectExceptionMessage('Cannot find station: 1.');

        $this->stationRepository->getById(1);
    }
}
