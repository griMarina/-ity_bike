<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\Reader;
use Grimarina\CityBike\Entities\Station;
use Grimarina\CityBike\Exceptions\{InvalidArgumentException, StationNotFoundException};

class StationsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }

    public function getEntries(): int
    {

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `stations`;");
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    public function getAll(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare("SELECT id, name_fi as `name`, address_fi as `address`, capacity, coordinate_x, coordinate_y FROM `stations` 
        ORDER BY id ASC LIMIT :offset, :limit ;");

        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?Station
    {
        $stmt = $this->pdo->prepare("SELECT stations.id, stations.name_fi, stations.address_fi, stations.capacity, stations.coordinate_x, stations.coordinate_y
        FROM stations
        WHERE stations.id = :id
        GROUP BY stations.id, stations.name_fi, stations.address_fi, stations.capacity, stations.coordinate_x, stations.coordinate_y;");

        $stmt->execute(
            [
                ':id' => (int) $id
            ]
        );

        $result = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Station::class)[0] ?? null;

        if ($result === null) {
            $message = "Cannot find station: $id";
            throw new StationNotFoundException($message);
        }

        return $result;
    }

    public function getAddInfoById(int $id): array
    {
        // Get total number of journeys and the average distance of a journey starting from the station
        $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT id) AS total_start, AVG(distance) as avg_distance_start FROM trips WHERE departure_station_id = :id");
        $stmt->execute([':id' => $id]);
        $tripsStart = $stmt->fetch(\PDO::FETCH_ASSOC);
        $tripsStart['avg_distance_start'] = round(($tripsStart['avg_distance_start'] / 1000), 2);

        // Get total number of journeys and the average distance of a journey ending at the station
        $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT id) AS total_end, AVG(distance) as avg_distance_end FROM trips WHERE return_station_id = :id");
        $stmt->execute([':id' => $id]);
        $tripsEnd = $stmt->fetch(\PDO::FETCH_ASSOC);
        $tripsEnd['avg_distance_end'] = round(($tripsEnd['avg_distance_end'] / 1000), 2);

        // Get the top 5 most popular return stations for journeys starting from the station
        $stmt = $this->pdo->prepare("SELECT return_station_id, return_station_name FROM trips WHERE departure_station_id = :id GROUP BY return_station_id, return_station_name ORDER BY COUNT(*)  DESC LIMIT 5");
        $stmt->execute([':id' => $id]);
        $topReturnStations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get the top 5 most popular departure stations for journeys ending at the station
        $stmt = $this->pdo->prepare("SELECT departure_station_id,departure_station_name FROM trips WHERE return_station_id = :id GROUP BY departure_station_id, departure_station_name ORDER BY COUNT(*)  DESC LIMIT 5");
        $stmt->execute([':id' => $id]);
        $topDepartureStations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [
            'total_start' => (int) $tripsStart['total_start'],
            'total_end' => (int) $tripsEnd['total_end'],
            'avg_distance_start' => (float) $tripsStart['avg_distance_start'],
            'avg_distance_end' => (float) $tripsEnd['avg_distance_end'],
            'top_return_stations' => (array) $topReturnStations,
            'top_departure_stations' => (array) $topDepartureStations,
        ];

        return $result;
    }

    public function importCsv(Reader $csv): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO stations (id, name_fi, name_sv, name_en, address_fi, address_sv, city_fi, city_sv, operator, capacity, coordinate_x, coordinate_y) VALUES (:id, :name_fi, :name_sv, :name_en, :address_fi, :address_sv, :city_fi, :city_sv, :operator, :capacity, :coordinate_x, :coordinate_y)");

        foreach ($csv as $row) {
            try {
                $stmt->execute(
                    [
                        ':id' => (int) $row['ID'],
                        ':name_fi' => (string) $row['Nimi'],
                        ':name_sv' => (string) $row['Namn'],
                        ':name_en' => (string) $row['Name'],
                        ':address_fi' => (string) $row['Osoite'],
                        ':address_sv' => (string) $row['Adress'],
                        ':city_fi' => (string) $row['Kaupunki'],
                        ':city_sv' => (string) $row['Stad'],
                        ':operator' => (string) $row['Operaattor'],
                        ':capacity' => (int) $row['Kapasiteet'],
                        ':coordinate_x' => (float) $row['x'],
                        ':coordinate_y' => (float) $row['y']
                    ]
                );
            } catch (\Error $e) {
                throw new InvalidArgumentException('File contains invalid data');
            }
        }
    }
}
