<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Exception;

class ContentController extends Controller
{
         /**
    * @OA\Get(
    *     path="/api/v1/content/list",
    *     summary="Mostrar Los tipos de contenidos",
    *     tags={"Content"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los tipos de contenidos.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "roles": {
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
            $content = Content::all('id', 'name');
            return response()->json(["contents" => $content], 200);
        } catch (Exception $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
}
