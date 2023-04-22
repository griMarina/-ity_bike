<?php

namespace Grimarina\CityBike\http\Actions\Stations;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;
use Grimarina\CityBike\Exceptions\{StationNotFoundException, HttpException};
use Grimarina\CityBike\http\Actions\ActionInterface;

class FindById implements ActionInterface
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
            'start_trips' => $station->getStartTrips(),
            'end_trips' => $station->getEndTrips(),
        ]);
    }
}
