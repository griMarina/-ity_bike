<?php

namespace Grimarina\CityBike\Entities;

class Station
{

    public function __construct(
        private int $id = 0,
        private string $name_fi = '',
        private string $name_se = '',
        private string $name_en = '',
        private string $address_fi = '',
        private string $address_se = '',
        private string $city_fi = '',
        private string $city_sv = '',
        private string $operator = '',
        private int $capacity = 0,
        private float $coordinate_x = 0.0,
        private float $coordinate_y = 0.0,
        private int $start_trips = 0,
        private int $end_trips = 0,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name_fi;
    }

    public function getAddress(): string
    {
        return $this->address_fi;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getCoordinateX(): float
    {
        return $this->coordinate_x;
    }

    public function getCoordinateY(): float
    {
        return $this->coordinate_y;
    }

    public function getStartTrips(): int
    {
        return $this->start_trips;
    }

    public function getEndTrips(): int
    {
        return $this->end_trips;
    }

    public function __toString(): string
    {
        return $this->name_fi . ' ' . $this->address_fi . PHP_EOL;
    }
}
