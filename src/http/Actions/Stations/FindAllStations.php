<?php

namespace Grimarina\CityBike\http\Actions\Stations;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\NotFoundException;
use Grimarina\CityBike\http\Actions\ActionInterface;

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
        } catch (NotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
