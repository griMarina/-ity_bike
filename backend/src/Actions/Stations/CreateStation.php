<?php

namespace Grimarina\CityBike\Actions\Stations;

use Grimarina\CityBike\Actions\ActionInterface;
use Grimarina\CityBike\Entities\Station;
use Grimarina\CityBike\Exceptions\HttpException;
use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\StationsRepository;


class CreateStation implements ActionInterface
{
    public function __construct(
        private StationsRepository $stationsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $station = new Station(
                $request->jsonBodyField('name_fi'),
                $request->jsonBodyField('name_sv'),
                $request->jsonBodyField('name_en'),
                $request->jsonBodyField('address_fi'),
                $request->jsonBodyField('address_sv'),
                $request->jsonBodyField('city_fi'),
                $request->jsonBodyField('city_sv'),
                $request->jsonBodyField('operator'),
                $request->jsonBodyField('capacity'),
            );
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->stationsRepository->save($station);

        return new SuccessfulResponse([
            'message' => 'New station is added!',
        ]);
    }
}
