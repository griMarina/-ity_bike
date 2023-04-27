<?php

namespace Grimarina\CityBike\Actions\Stations;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\{StationNotFoundException, HttpException};
use Grimarina\CityBike\Actions\ActionInterface;

class FindAllStations implements ActionInterface
{
    public function __construct(
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $page = $request->query('page');
            $page = ($page > 0) ? $page : 1;

            $limit = $request->query('limit');
            $limit = ($limit > 0) ? $limit : 1;
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $stations = $this->stationsRepository->getAll($page, $limit);
            return new SuccessfulResponse($stations);
        } catch (StationNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
