<?php

namespace Grimarina\CityBike\Entities;

class Trip
{
    public function __construct(
        private string $departure,
        private string $return,
        private string $departure_station_id,
        private string $departure_station_name,
        private string $return_station_id,
        private string $return_station_name,
        private int $distance,
        private int $duration
    ) {
    }

    public function __toString(): string
    {
        return $this->departure_station_name . '>>>' . $this->return_station_name . PHP_EOL;
    }
}
