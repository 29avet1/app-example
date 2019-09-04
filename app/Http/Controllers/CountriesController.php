<?php

namespace App\Http\Controllers;

use App\Country;

class CountriesController extends Controller
{
    /**
     * Get all countries
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *      path="/countries",
     *      tags={"Countries"},
     *      operationId="api.countries.get",
     *      summary="Get all countries",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="array",
     *            @SWG\Items(
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="code", type="string"),
     *            )
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
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
