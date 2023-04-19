<?php

declare(strict_types=1);

namespace Grimarina\CityBike\http\Actions;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\{ImportException, CsvFileException};
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\ResultSet;


class ImportTrips implements ActionInterface
{
    public function __construct(
        private string $filename,
        private TripsRepository $tripsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $csv = Reader::createFromPath($this->filename, 'r');
            $csv->setHeaderOffset(0);
            $validCsv = $this->validateCsv($csv);
            $this->tripsRepository->importCsv($validCsv);
            return new SuccessfulResponse(['message' => 'CSV file imported successfully']);
        } catch (ImportException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    protected function validateCsv(Reader $csv): ResultSet
    {
        $statement = (new Statement())
            ->offset(1) // Skip header row
            ->where(function (array $row) {
                return ($row['Duration (sec.)'] >= 10) && ($row['Covered distance (m)'] >= 10);
            });

        $validCsv = $statement->process($csv);
        return $validCsv;
    }
}
