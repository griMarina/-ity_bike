<?php

namespace Grimarina\CityBike\Repositories;

use League\Csv\Reader;
use Grimarina\CityBike\Exceptions\InvalidArgumentException;

class StationsRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
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
