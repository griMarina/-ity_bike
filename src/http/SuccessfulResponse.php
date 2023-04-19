<?php

namespace Grimarina\CityBike\http;

use Grimarina\CityBike\http\Response;

class SuccessfulResponse extends Response
{
    protected const SUCCESS = true;

    public function __construct(
        // Successful response contains an array with data, empty by default
        private array $data = []
    ) {
    }

    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}
