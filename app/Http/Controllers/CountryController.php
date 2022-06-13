<?php

namespace App\Http\Controllers;

use App\Models\Country;

use Illuminate\Http\Request;
use Exception;


class CountryController extends Controller
{

    /**
    * @OA\Get(
    *     path="/api/v1/countries",
    *     summary="Mostrar Paises",
    *     tags={"Countries"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los paises."
    *     )
    * )
    */
    public function index(Request $request)
    {
        try {
            $country = Country::all();
            return response()->json(["countries" => $country], 200);
        } catch (Exception $th) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
