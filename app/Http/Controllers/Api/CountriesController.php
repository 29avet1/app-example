<?php

namespace App\Http\Controllers\Api;

use App\Country;

class CountriesController extends ApiController
{
    /**
     * Get all countries
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $countriesArray = Country::orderBy('id')->pluck('name', 'code')->toArray();
        $countries = [];

        foreach ($countriesArray as $code => $country) {
            $countries[] = [
                'name' => $country,
                'code' => $code,
            ];
        }

        return response()->json($countries);
    }
}
