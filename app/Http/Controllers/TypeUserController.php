<?php

namespace App\Http\Controllers;

use App\Models\TypesUsers;

use Illuminate\Http\Request;
use Exception;

class TypeUserController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/v1/types_users/list",
    *     summary="Mostrar Tipos de Usuarios",
    *     tags={"Type Users"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los tipos de usuarios.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "types_users": {
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
            $typeUsers = TypesUsers::all('id', 'name');
            return response()->json(["types_users" => $typeUsers], 200);
        } catch (Exception $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
}
