<?php

namespace Grimarina\CityBike\http;

use Grimarina\CityBike\http\Response;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        // An error response contains a string with the error message, 'Something goes wrong' by default
        private string $reason = 'Something went wrong'
    ) {
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
