<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $categorias = Category::where("name", $request->input("name"))->first();
            if (!empty($categorias)) {
                throw new Exception("Ya existe una categoria con el nombre " . $request->input("name"));
            }
            $datosCategory = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosCategory['file_path'] = $request->input("file_path");
            }

            Category::create($datosCategory);
            return response()->json(["message" => "Categoria creada con éxito"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $subEmpresa = Category::find($id);
            $datosActualizar = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosActualizar['file_path'] = $request->input("file_path");
            }
            if (empty($subEmpresa)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $subEmpresa->update($datosActualizar);
                $message = "Categoria actualizada con éxto";
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function index(Request $request)
    {
        try {

            return response()->json(["categorias" => Category::all()], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Category::find($id);
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
