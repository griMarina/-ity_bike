<?php

namespace Grimarina\CityBike\Actions\Stations;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\{StationNotFoundException, HttpException};
use Grimarina\CityBike\Actions\ActionInterface;

class FindStationById implements ActionInterface
{
    public function __construct(
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {

        try {
            $id = (int) $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $station = $this->stationsRepository->getById($id);
            $addInfo = $this->stationsRepository->getAddInfoById($id);
        } catch (StationNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $station->getId(),
            'name' => $station->getName(),
            'address' => $station->getAddress(),
            'capacity' => $station->getCapacity(),
            'x' => $station->getCoordinateX(),
            'y' => $station->getCoordinateY(),
            'total_start' => $addInfo['total_start'],
            'total_end' => $addInfo['total_end'],
            'avg_distance_start' => $addInfo['avg_distance_start'],
            'avg_distance_end' => $addInfo['avg_distance_end'],
            'top_return_stations' => $addInfo['top_return_stations'],
            'top_departure_stations' => $addInfo['top_departure_stations'],
        ]);
    }
}
