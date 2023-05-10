<?php

declare(strict_types=1);

namespace Grimarina\CityBike\Commands;

use Grimarina\CityBike\Exceptions\ImportException;
use Grimarina\CityBike\Repositories\TripsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;

class ImportTripsCommand extends Command
{
    private $tripsRepository;

    public function __construct(TripsRepository $tripsRepository)
    {
        $this->tripsRepository = $tripsRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('trips:import')
            ->setDescription('Import trips from a file to the database')
            ->addArgument('file', InputArgument::REQUIRED, 'The file name to import from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath =  TRIPS_DIR . $input->getArgument('file');

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
            $output->writeln('Invalid file extension. Only CSV files are allowed.');
            return Command::FAILURE;
        }

        if (!file_exists($filePath)) {
            $output->writeln('File not found. Please enter a valid file name.');
            return Command::FAILURE;
        }

        try {
            $csv = Reader::createFromPath($filePath);
            $csv->setHeaderOffset(0);

            $start = microtime(true);

            $rows = $this->tripsRepository->importCsv($csv);

            $end = microtime(true);
            $time = (int) round($end - $start);
        } catch (ImportException $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("Imported {$rows} trips in {$this->formatTime($time)}.");
        return Command::SUCCESS;
    }

    protected function formatTime(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        $output = '';
        if ($minutes > 0) {
            $output .= $minutes . ' min ';
        }
        $output .= $seconds . ' sec';
        return $output;
    }
}
