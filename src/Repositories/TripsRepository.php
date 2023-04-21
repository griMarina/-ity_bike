<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\ResultSet;
use League\Csv\Reader;
use League\Csv\Statement;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;

class TripsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }

    public function importCsv(Reader $csv): void
    {
        ini_set('max_execution_time', 300);

        $validCsv = $this->validateCsv($csv);

        $batchSize = 1000; // number of rows to insert in each batch
        $batch = [];

        $stmt = $this->pdo->prepare("INSERT IGNORE INTO trips (departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration) VALUES (:departure, :return, :departure_station_id, :departure_station_name, :return_station_id, :return_station_name, :distance, :duration)");

        foreach ($validCsv as $row) {
            $batch[] = [
                ':departure' => (string) $row['Departure'],
                ':return' => (string) $row['Return'],
                ':departure_station_id' => (string) $row['Departure station id'],
                ':departure_station_name' => (string) $row['Departure station name'],
                ':return_station_id' => (string) $row['Return station id'],
                ':return_station_name' => (string) $row['Return station name'],
                ':distance' => (int) $row['Covered distance (m)'],
                ':duration' => (int) $row['Duration (sec.)']
            ];

            if (count($batch) === $batchSize) {
                $this->executeBatch($stmt, $batch);
                $batch = [];
            }

            if (count($batch) > 0) {
                $this->executeBatch($stmt, $batch);
            }
        }
    }

    private function executeBatch(\PDOStatement $stmt, array $batch): void
    {
        $this->pdo->beginTransaction();
        foreach ($batch as $row) {
            $stmt->execute($row);
        }
        $this->pdo->commit();
    }

    public function validateCsv(Reader $csv): ResultSet
    {
        $statement = (new Statement())
            ->where(function (array $row) {
                if (!isset($row['Duration (sec.)']) || !isset($row['Covered distance (m)'])) {
                    throw new InvalidArgumentException('File contains invalid data');
                }
                return ($row['Duration (sec.)'] >= 10) && ($row['Covered distance (m)'] >= 10);
            });

        $validCsv = $statement->process($csv);
        return $validCsv;
    }
}
