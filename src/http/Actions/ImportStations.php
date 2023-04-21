<?php

declare(strict_types=1);

namespace Grimarina\CityBike\http\Actions;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\ImportException;
use League\Csv\Reader;

class ImportStations implements ActionInterface
{

    public function __construct(
        private string $filename,
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $csv = Reader::createFromPath($this->filename);
            $csv->setHeaderOffset(0);
            $this->stationsRepository->importCsv($csv);
            return new SuccessfulResponse(['message' => 'CSV file imported successfully']);
        } catch (ImportException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
