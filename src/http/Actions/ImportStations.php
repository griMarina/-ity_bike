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
            $csv = Reader::createFromPath($this->filename, 'r');
            $csv->setHeaderOffset(0);
            $this->stationsRepository->importCsv($csv);
            return new SuccessfulResponse(['message' => 'CSV file imported successfully']);
        } catch (ImportException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    // protected function validateCsv(): void
    // {
    //     $csv = array_map('str_getcsv', file($this->csvPath));
    //     array_shift($csv); // remove header row

    //     foreach ($csv as $row) {
    //         if (count($row) !== 13) {
    //             throw new Exception('Invalid CSV file: Each row must have 13 columns');
    //         }

    //         if (!is_numeric($row[0]) || !is_numeric($row[1]) || !is_numeric($row[2]) || !is_numeric($row[3])) {
    //             throw new Exception('Invalid CSV file: All columns must be numeric');
    //         }

    //         // [$startTime, $startX, $startY, $endTime, $endX, $endY] = $row;

    //         // $duration = $endTime - $startTime;
    //         // $distance = sqrt(($endX - $startX) ** 2 + ($endY - $startY) ** 2);

    //         // if ($duration < self::MIN_DURATION) {
    //         //     throw new Exception('Invalid CSV file: Journey duration must be at least 10 seconds');
    //         // }

    //         // if ($distance < self::MIN_DISTANCE) {
    //         //     throw new Exception('Invalid CSV file: Journey distance must be at least 10 meters');
    //         // }
    //     }

    //     $this->csvPath = $csv;
    // }

    // private function importCsv(): void
    // {
    //     $stmt = $this->pdo->prepare('INSERT INTO journeys (start_time, start_x, start_y, end_time, end_x, end_y) VALUES (?, ?, ?, ?, ?, ?)');

    //     $csv = array_map('str_getcsv', file($this->csvPath));
    //     array_shift($csv); // remove header row

    //     foreach ($csv as $row) {
    //         [$startTime, $startX, $startY, $endTime, $endX, $endY] = $row;

    //         $stmt->execute([$startTime, $startX, $startY, $endTime, $endX, $endY]);
    //     }
    // }
}
