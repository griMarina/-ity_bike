<?php

namespace Commands;

use PHPUnit\Framework\TestCase;
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Commands\ImportStationsCommand;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ImportStationsCommandTest extends TestCase
{
    private $stationsRepository;
    private $command;

    protected function setUp(): void
    {
        $this->stationsRepository = $this->createMock(StationsRepository::class);
        $this->command = new ImportStationsCommand($this->stationsRepository);
    }

    public function testItReturnsFailureIfTheFileDoesNotExist()
    {
        define('STATIONS_DIR', dirname(dirname(__DIR__)) . '/data/stations/');

        $input = new ArrayInput([
            'file' => 'nonexistentfile.csv',
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('File not found', $output->fetch());
    }

    public function testItReturnsFailureIfTheFileHasInvalidExtension()
    {
        $input = new ArrayInput([
            'file' => 'stations.txt',
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Invalid file extension', $output->fetch());
    }

    public function testItReturnsSuccessIfFileIsValid()
    {
        $this->stationsRepository->expects($this->once())
            ->method('importCsv')
            ->with($this->isInstanceOf(Reader::class));

        $input = new ArrayInput([
            'file' => 'stations.csv',
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertStringContainsString('Stations imported successfully', $output->fetch());
    }
}
