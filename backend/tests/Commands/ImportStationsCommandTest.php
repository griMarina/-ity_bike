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
        // Create a mock instance of StationsRepository and instantiate the command
        $this->stationsRepository = $this->createMock(StationsRepository::class);
        $this->command = new ImportStationsCommand($this->stationsRepository);
    }

    public function testItReturnsFailureIfTheFileDoesNotExist()
    {
        define('STATIONS_DIR', dirname(dirname(__DIR__)) . '/data/stations/');

        // Define the input and output for the test
        $input = new ArrayInput([
            'file' => 'nonexistentfile.csv',
        ]);
        $output = new BufferedOutput();

        // Execute the command and capture the result
        $result = $this->command->run($input, $output);

        // Assert the result and the output message
        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('File not found', $output->fetch());
    }

    public function testItReturnsFailureIfTheFileHasInvalidExtension()
    {
        // Define the input and output for the test
        $input = new ArrayInput([
            'file' => 'stations.txt',
        ]);
        $output = new BufferedOutput();

        // Execute the command and capture the result
        $result = $this->command->run($input, $output);

        // Assert the result and the output message
        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Invalid file extension', $output->fetch());
    }

    public function testItReturnsSuccessIfFileIsValid()
    {
        // Set up the expectation for the mock method
        $this->stationsRepository->expects($this->once())
            ->method('importCsv')
            ->with($this->isInstanceOf(Reader::class))
            ->willReturn(10);

        // Define the input and output for the test
        $input = new ArrayInput([
            'file' => 'stations.csv',
        ]);
        $output = new BufferedOutput();

        // Execute the command and capture the result
        $result = $this->command->run($input, $output);

        // Assert the result and the output message
        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertStringContainsString('Imported 10 stations in 0 sec.', $output->fetch());
    }
}
