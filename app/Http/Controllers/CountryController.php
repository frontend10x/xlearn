<?php

namespace App\Http\Controllers;

use App\Models\Country;

use Illuminate\Http\Request;
use Exception;


class CountryController extends Controller
{

    /**
    * @OA\Get(
    *     path="/api/v1/countries/list",
    *     summary="Mostrar Paises",
    *     tags={"Countries"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los paises.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "countries": {
    *                           {
    *                               "id": 0,
    *                               "name": ""
    *                           },
    *                         }
    *                   }
    *             )
    *         )
    *     )
    * )
    */
    public function index(Request $request)
    {
        try {
            $country = Country::all();
            return response()->json(["countries" => $country], 200);
        } catch (Exception $th) {
            return return_exceptions($e);
        }
    }
}
