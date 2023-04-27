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

    public function getAll(int $page, int $limit): array
    {
        // $limit = 20;
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
        $stmt = $this->pdo->prepare("SELECT stations.id, stations.name_fi, stations.address_fi, stations.capacity, stations.coordinate_x, stations.coordinate_y,
        (
            SELECT COUNT(DISTINCT id) 
            FROM trips
            WHERE departure_station_id = stations.id
        ) AS start_trips,
        (
            SELECT COUNT(DISTINCT id) 
            FROM trips
            WHERE return_station_id = stations.id
        ) AS end_trips
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
