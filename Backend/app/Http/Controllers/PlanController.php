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
                , "amount_people" => $request->input("amount_people")
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
            return response()->json(["message" => $e->getMessage()], 500);
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
                , "amount_people" => $request->input("amount_people")
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
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        try {
            return response()->json(["plan" => Plan::all()], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
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
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
