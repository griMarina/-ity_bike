<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use Grimarina\CityBike\Entities\Trip;
use InvalidArgumentException;

class TripsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }

    // Get the total number of trips in the database
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

        return  $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save(Trip $trip): void
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO trips (departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration) VALUES (:departure, :return, :departure_station_id, :departure_station_name, :return_station_id, :return_station_name, :distance, :duration)");

        try {
            $stmt->execute(
                [
                    ':departure' => (string) $trip->getDeparture(),
                    ':return' => (string) $trip->getReturn(),
                    ':departure_station_id' => (int) $trip->getDepartureStationId(),
                    ':departure_station_name' => (string) $trip->getDepartureStationName(),
                    ':return_station_id' => (int) $trip->getReturnStationId(),
                    ':return_station_name' => (string) $trip->getReturnStationName(),
                    ':distance' => (int) $trip->getDistance(),
                    ':duration' => (int) $trip->getDuration(),
                ]
            );
        } catch (\Error $e) {
            throw new InvalidArgumentException('Trip contains invalid data.');
        }
    }

    public function importCsv(Reader $csv): int
    {
        ini_set('max_execution_time', 300);

        // Validate the CSV file and get a filtered result set
        $validCsv = $this->validateCsv($csv);

        $batchSize = 5000; // number of rows to insert in each batch
        $batch = [];
        $count = 0;

        $stmt = $this->pdo->prepare("INSERT IGNORE INTO trips (departure, `return`, departure_station_id, departure_station_name, return_station_id, return_station_name, distance, duration) VALUES (:departure, :return, :departure_station_id, :departure_station_name, :return_station_id, :return_station_name, :distance, :duration)");

        foreach ($validCsv as $row) {
            // Add the current row data to the batch array
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

            // If the batch size is reached, execute the batch
            if (count($batch) === $batchSize) {
                $rows = $this->executeBatch($stmt, $batch);
                $count += $rows;
                $batch = [];
            }
        }

        // Execute the remaining rows in the batch
        if (count($batch) > 0) {
            $rows = $this->executeBatch($stmt, $batch);
            $count += $rows;
        }

        // Return the total count of imported trips
        return $count;
    }

    private function executeBatch(\PDOStatement $stmt, array $batch): int
    {
        $count = 0;
        $this->pdo->beginTransaction();
        foreach ($batch as $row) {
            $stmt->execute($row);
            $count += $stmt->rowCount();
        }
        $this->pdo->commit();
        return $count;
    }

    private function validateCsv(Reader $csv): ResultSet
    {
        // Create a statement object for filtering and validating rows
        $statement = (new Statement())
            ->where(function (array $row) {

                // Convert the 'Departure' and 'Return' values to DateTime objects
                $departure = \DateTime::createFromFormat('Y-m-d\TH:i:s', $row['Departure']);
                $return = \DateTime::createFromFormat('Y-m-d\TH:i:s', $row['Return']);

                // Check if the date parsing failed for either 'Departure' or 'Return'
                if ($departure === false || $return === false) {
                    return false;
                }

                // Check if 'Return' is earlier than 'Departure'
                if ($return < $departure) {
                    return false;
                }

                // Check if the required fields are numeric
                if (!ctype_digit($row['Departure station id']) || !ctype_digit($row['Return station id']) || !ctype_digit($row['Duration (sec.)']) || !ctype_digit($row['Covered distance (m)'])) {
                    return false;
                }

                // Convert the required fields to integers
                $duration = (int) $row['Duration (sec.)'];
                $distance = (int) $row['Covered distance (m)'];

                // Check if the duration or distance is below the minimum threshold
                if ($duration < 10 || $distance < 10) {
                    return false;
                }

                // Return the row if it passes all validations
                return $row;
            });

        // Process the CSV file using the statement to filter and validate rows
        $validCsv = $statement->process($csv);

        // Return the filtered and validated result set
        return $validCsv;
    }
}
