<?php

namespace Repositories;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\StationsRepository;
use League\Csv\Reader;

class StationsRepositoryTest extends TestCase
{
    private $statementMock;
    private $connectionStub;

    public function setUp(): void
    {
        $this->connectionStub = $this->createStub(\PDO::class);
        $this->statementMock = $this->createMock(\PDOStatement::class);
    }
    public function testImportCsvInsertsCorrectData()
    {
        $this->statementMock->expects($this->once())
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
                ':coordinate_x' => 60.1234,
                ':coordinate_y' => 24.5678,
            ]);

        $this->connectionStub->method('prepare')->willReturn($this->statementMock);

        $repository = new StationsRepository($this->connectionStub);

        $csv = Reader::createFromPath('tests/data/stations_valid.csv');
        $csv->setHeaderOffset(0);
        $repository->importCsv($csv);
    }

    public function testImportCsvThrowsExceptionWhenCsvContainsInvalidData()
    {
        // Create a new StationsRepository object with the mock PDO object
        $repository = new StationsRepository($this->connectionStub);

        // Create a mock CSV object with invalid data
        $csv = Reader::createFromPath('tests/data/stations_invalid.csv');

        // Expect an exception to be thrown when the importCsv() method is called
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('File contains invalid data');

        $repository->importCsv($csv);
    }
}
