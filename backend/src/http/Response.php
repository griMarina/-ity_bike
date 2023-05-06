<?php

namespace Grimarina\CityBike\http;

abstract class Response
{
    protected const SUCCESS = true;

    public function send(): void
    {
        $data = ['success' => static::SUCCESS] + $this->payload();

        header('Content-Type: application/json');
        http_response_code($this->status());

        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    abstract public function payload(): array;

    abstract public function status(): int;
}
