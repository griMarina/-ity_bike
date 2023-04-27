<?php

namespace Grimarina\CityBike\Actions\Trips;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\{TripNotFoundException, HttpException};
use Grimarina\CityBike\Actions\ActionInterface;

class FindTripById implements ActionInterface
{
    public function __construct(
        private TripsRepository $tripsRepository
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
            $trip = $this->tripsRepository->getById($id);
        } catch (TripNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $trip->getId(),
            'departure' => $trip->getDeparture(),
            'return' => $trip->getReturn(),
            'departure_station_id' => $trip->getDepartureStationId(),
            'departure_station_name' => $trip->getDepartureStationName(),
            'return_station_id' => $trip->getReturnStationId(),
            'return_station_name' => $trip->getReturnStationName(),
            'distance' => $trip->getDistance(),
            'duration' => $trip->getDuration(),
        ]);
    }
}
