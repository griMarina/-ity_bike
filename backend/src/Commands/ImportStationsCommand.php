<?php

declare(strict_types=1);

namespace Grimarina\CityBike\Commands;

use Grimarina\CityBike\Exceptions\ImportException;
use Grimarina\CityBike\Repositories\StationsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;

class ImportStationsCommand extends Command
{
    private $stationsRepository;

    public function __construct(StationsRepository $stationsRepository)
    {
        $this->stationsRepository = $stationsRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('stations:import')
            ->setDescription('Import stations from a file to the database')
            ->addArgument('file', InputArgument::REQUIRED, 'The file name to import from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath =  STATIONS_DIR . $input->getArgument('file');

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
            $this->stationsRepository->importCsv($csv);
        } catch (ImportException $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln('Stations imported successfully.');
        return Command::SUCCESS;
    }
}
