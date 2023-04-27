<?php

namespace Grimarina\CityBike\Entities;

class Trip
{

    public function __construct(
        private int $id = 0,
        private string $departure = '',
        private string $return = '',
        private string $departure_station_id = '',
        private string $departure_station_name = '',
        private string $return_station_id = '',
        private string $return_station_name = '',
        private int $distance = 0,
        private int $duration = 0
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDeparture(): string
    {
        return $this->departure;
    }

    public function getDepartureStationId(): string
    {
        return $this->departure_station_id;
    }

    public function getDepartureStationName(): string
    {
        return $this->departure_station_name;
    }

    public function getReturn(): string
    {
        return $this->return;
    }

    public function getReturnStationId(): string
    {
        return $this->return_station_id;
    }

    public function getReturnStationName(): string
    {
        return $this->return_station_name;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function __toString(): string
    {
        return $this->departure_station_name . '>>>' . $this->return_station_name . PHP_EOL;
    }
}
