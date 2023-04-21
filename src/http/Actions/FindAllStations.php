<?php

namespace Grimarina\CityBike\http\Actions;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\NotFoundException;

class FindAllStations implements ActionInterface
{
    public function __construct(
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $stations = $this->stationsRepository->getAll();
            return new SuccessfulResponse($stations);
        } catch (NotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
