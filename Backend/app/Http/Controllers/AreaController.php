<?php

namespace App\Http\Controllers;

use App\Models\Areas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $areas = Areas::where("name", $request->input("name"))->first();
            if (!empty($areas)) {
                throw new Exception("Ya existe un area con el nombre " . $request->input("name"));
            }
            $datosSubEmpresa = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }

            Areas::create($datosSubEmpresa);
            return response()->json(["message" => "Area creada con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $subEmpresa = Areas::find($id);
            $datosSubEmpresa = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }
            if (empty($subEmpresa)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $subEmpresa->update($datosSubEmpresa);
                $message = "Area actualizada con éxto";
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {

            return response()->json(["areas" => Areas::all()], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Areas::find($id);
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
