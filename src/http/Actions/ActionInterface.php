<?php

namespace Grimarina\CityBike\http\Actions;

use Grimarina\CityBike\http\{Request, Response};

interface ActionInterface
{
    public function handle(Request $request): Response;
}
