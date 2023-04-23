<?php

namespace Grimarina\CityBike\Actions;

use Grimarina\CityBike\http\{Request, Response};

interface ActionInterface
{
    public function handle(Request $request): Response;
}
