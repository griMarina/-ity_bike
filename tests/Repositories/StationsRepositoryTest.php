<?php

namespace Repositories;

use PHPUnit\Framework\TestCase;
use League\Csv\Reader;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;

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
    public function testImportCsvInsertsCorrectData()
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

        $this->stationRepository->importCsv($csv);
    }
}
