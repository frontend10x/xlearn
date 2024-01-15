<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function store(Request $request)
    {
        try {
            $plan = Plan::where("name", $request->input("name"))->first();
            if (!empty($plan)) {
                throw new Exception("Ya existe un plan con el nombre " . $request->input("name"));
            }
            $datosSubEmpresa = [
                "name" => $request->input("name")
                , "description" => $request->input("description")
                , "price" => $request->input("price")
                , "amount_user" => $request->input("amount_user")
                , "amount_time" => $request->input("amount_time")
                , "state" => $request->input("state")
                , "color_title" => $request->input("color_title")
                , "color_border" => $request->input("color_border")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }

            Plan::create($datosSubEmpresa);
            return response()->json(["message" => "Plan creado con éxito"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $plan = Plan::find($id);
            $datosSubEmpresa = [
                "name" => $request->input("name")
                , "description" => $request->input("description")
                , "price" => $request->input("price")
                , "amount_user" => $request->input("amount_user")
                , "amount_time" => $request->input("amount_time")
                , "state" => $request->input("state")
                , "color_title" => $request->input("color_title")
                , "color_border" => $request->input("color_border")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }
            if (empty($plan)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $plan->update($datosSubEmpresa);
                $message = "Plan actualizada con éxto";
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/plan/list",
    *     summary="Mostrar Planes",
    *     tags={"Plans"},
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los planes.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "plans": {
    *                           {
    *                               "id": 0,
    *                               "name": "",
    *                               "description": "",
    *                               "price": "",
    *                               "amount_user": "",
    *                               "amount_time": "",
    *                               "color_title": "",
    *                               "color_border": ""
    *                           },
    *                         }
    *                   }
    *             )
    *         )
    *     )
    * )
    */
    public function index()
    {
        try {

            $plans =  Plan::where('state', 1)->get();
            return response()->json(["plans" =>$plans], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function generality()
    {
        try {
            $plans = [
                [
                    "id" => 1, "name" => "Basic", "price" => 100000,
                    "feature" => ["Acceso a todos los cursos", "Soporte 8/5 (de 9 a 5 de la tarde)"]
                ],
                [
                    "id" => 2, "name" => "Business", "price" => 100000,
                    "feature" => [
                        "Acceso a todos los cursos", 
                        "Acceso comercial",
                        "Consultor temático 4 horas",
                        "video reto interno",
                        "Exploración cursos a medida",
                        "Soporte 24/7",
                        "Diagnostico requerimientos de formación"
                        ]
                ]
            ];
            return response()->json(["plans" =>$plans], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Plan::find($id);
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el Id:" . $id . " para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}