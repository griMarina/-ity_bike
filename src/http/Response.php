<?php

namespace Grimarina\CityBike\http;

// An abstract Response class containing the common functionality of a successful and error response
abstract class Response
{
    // Marking the success of the response
    protected const SUCCESS = true;

    // Method for sending a response
    public function send(): void
    {
        // Response data: success marking and payload
        $data = ['success' => static::SUCCESS] + $this->payload();

        // send a header saying that the body of the response will be JSON
        header('Content-Type: application/json');

        // encode the data in JSON and send it in the response body
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    // Declaring an abstract method that returns a response payload
    abstract protected function payload(): array;
}
