<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use Grimarina\CityBike\Entities\Trip;
use Grimarina\CityBike\Exceptions\{InvalidArgumentException, TripNotFoundException};

class TripsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }

    public function getEntries(): int
    {

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `trips`;");
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    public function getAll(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare("SELECT id, departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration FROM `trips` LIMIT :offset, :limit;");

        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as &$row) {
            $row['distance'] = round(($row['distance'] / 1000), 2);
            $row['duration'] = $row['duration'] / 60;
        }
        return $result;
    }

    public function getById(int $id): ?Trip
    {
        $stmt = $this->pdo->prepare("SELECT id, departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration FROM `trips` WHERE trips.id = :id");

        $stmt->execute(
            [
                ':id' => (int) $id
            ]
        );

        $result = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Trip::class)[0] ?? null;

        if ($result === null) {
            $message = "Cannot find trip: $id";
            throw new TripNotFoundException($message);
        }

        return $result;
    }

    public function importCsv(Reader $csv): void
    {
        ini_set('max_execution_time', 300);

        $validCsv = $this->validateCsv($csv);

        $batchSize = 5000; // number of rows to insert in each batch
        $batch = [];

        $stmt = $this->pdo->prepare("INSERT IGNORE INTO trips (departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration) VALUES (:departure, :return, :departure_station_id, :departure_station_name, :return_station_id, :return_station_name, :distance, :duration)");

        foreach ($validCsv as $row) {
            $batch[] = [
                ':departure' => $row['Departure'],
                ':return' => $row['Return'],
                ':departure_station_id' => (int) $row['Departure station id'],
                ':departure_station_name' => (string) $row['Departure station name'],
                ':return_station_id' => (int) $row['Return station id'],
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

    public function validateCsv(Reader $csv): ResultSet
    {
        $statement = (new Statement())
            ->where(function (array $row) {
                if (!isset($row['Duration (sec.)']) || !isset($row['Covered distance (m)'])) {
                    throw new InvalidArgumentException('File contains invalid data');
                }

                try {
                    new \DateTime($row['Departure']);
                    new \DateTime($row['Return']);
                } catch (InvalidArgumentException $e) {
                    return false; // skip row if departure or return isn't parseable
                }

                return ($row['Duration (sec.)'] >= 10) && ($row['Covered distance (m)'] >= 10);
            });


        $validCsv = $statement->process($csv);
        return $validCsv;
    }
}
