<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\ResultSet;

class TripsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }

    public function importCsv(ResultSet $csv): void
    {
        ini_set('max_execution_time', 120);

        $batchSize = 5000; // number of rows to insert in each batch
        $batch = [];

        $stmt = $this->pdo->prepare("INSERT INTO trips (departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration) VALUES (:departure, :return, :departure_station_id, :departure_station_name, :return_station_id, :return_station_name, :distance, :duration)");

        foreach ($csv as $row) {
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
        }

        if (count($batch) > 0) {
            $this->executeBatch($stmt, $batch);
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
}
