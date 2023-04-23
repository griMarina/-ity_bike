<?php

namespace Grimarina\CityBike\Actions\Stations;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\StationNotFoundException;
use Grimarina\CityBike\Actions\ActionInterface;

class FindAllStations implements ActionInterface
{
    public function __construct(
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        $page = $request->query('page');

        try {
            $stations = $this->stationsRepository->getAll($page);
            return new SuccessfulResponse($stations);
        } catch (StationNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
