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
            $limit = $request->query('limit');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if (!filter_var($page, FILTER_VALIDATE_INT) || !filter_var($limit, FILTER_VALIDATE_INT)) {
            return new ErrorResponse('Invalid parameters.');
        }

        try {
            $entries = $this->stationsRepository->getEntries();
            $stations = $this->stationsRepository->getAll($page, $limit);

            if (empty($stations)) {
                return new ErrorResponse('No stations found.', 404);
            }

            $data['entries'] = $entries;
            $data['stations'] = $stations;
            return new SuccessfulResponse($data);
        } catch (StationNotFoundException $e) {
            return new ErrorResponse($e->getMessage(), 404);
        }
    }
}
