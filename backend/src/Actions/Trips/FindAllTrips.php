<?php

namespace Grimarina\CityBike\Actions\Trips;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\{TripNotFoundException, HttpException};
use Grimarina\CityBike\Actions\ActionInterface;

class FindAllTrips implements ActionInterface
{
    public function __construct(
        private TripsRepository $tripsRepository
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
            $entries = $this->tripsRepository->getEntries();
            $trips = $this->tripsRepository->getAll($page, $limit);

            if (empty($trips)) {
                return new ErrorResponse('No trips found.', 404);
            }

            $data['entries'] = $entries;
            $data['trips'] = $trips;
            return new SuccessfulResponse($data);
        } catch (TripNotFoundException $e) {
            return new ErrorResponse($e->getMessage(), 404);
        }
    }
}
