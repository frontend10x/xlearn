<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use Exception;

class SizeController extends Controller
{
     /**
    * @OA\Get(
    *     path="/api/v1/size/list",
    *     summary="Mostrar Los Tamaños de empresas",
    *     tags={"Company Size"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los tamaños de empresas.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "roles": {
    *                           {
    *                               "id": 0,
    *                               "range": "",
    *                               "tag": "",
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
            $size = Size::all('id', 'range_size', 'tag');
            return response()->json(["sizes" => $size], 200);
        } catch (Exception $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
}
