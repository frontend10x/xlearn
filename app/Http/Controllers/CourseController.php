<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store(Request $request)
    {
        try {
            $courses = Course::where("name", $request->input("name"))->first();
            if (!empty($courses)) {
                throw new Exception("Ya existe un cursor con el nombre " . $request->input("name"));
            }
            $datosSubEmpresa = [
                "name" => $request->input("name")
                , "description" => $request->input("description")
                , "state" => $request->input("state")
                , "free_video" => $request->input("free_video")
                , "video_path" => $request->input("video_path")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }

            Course::create($datosSubEmpresa);
            return response()->json(["message" => "Curso creado con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $datosInsertar = Course::find($id);
            $data = [
                "name" => $request->input("name")
                , "description" => $request->input("description")
                , "state" => $request->input("state")
                , "free_video" => $request->input("free_video")
                , "video_path" => $request->input("video_path")
            ];
            if (!empty($request->input("area_id"))) {
                $data['area_id'] = $request->input("area_id");
            }
            if (!empty($request->input("file_path"))) {
                $data['file_path'] = $request->input("file_path");
            }
            if (empty($datosInsertar)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $datosInsertar->update($data);
                $message = "Curso actualizado con éxto";
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            return response()->json(["cursos" => Course::all()], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Course::find($id);
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el Id:" . $id . " para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    
    public function userrating(Request $request, $id)
    {
        try {
            $buscaActualiza = Course::find($id);
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
