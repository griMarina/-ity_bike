<?php

namespace Grimarina\CityBike\Entities;

class Station
{
    public function __construct(
        private int $id,
        private string $name_fi,
        private string $name_se,
        private string $name_en,
        private string $address_fi,
        private string $address_se,
        private string $city_fi,
        private string $city_sv,
        private string $operator,
        private int $capacity,
        private float $coordinate_x,
        private float $coordinate_y,
    ) {
    }

    public function __toString(): string
    {
        return $this->name_fi . ' ' . $this->address_fi . PHP_EOL;
    }
}
