<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Lesson_user_comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function store(Request $request)
    {
        try {
            $lesson = Lesson::where("name", $request->input("name"))->first();
            if (!empty($lesson)) {
                throw new Exception("Ya existe una lección con el nombre " . $request->input("name"));
            }
            $datosInsert = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state"), "free_video" => $request->input("free_video"), "video_path" => $request->input("video_path")
            ];
            if (!empty($request->input("file_path"))) {
                $datosInsert['file_path'] = $request->input("file_path");
            }

            Lesson::create($datosInsert);
            return response()->json(["message" => "Leccion creada con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $existencia = Lesson::find($id);
            $dataUpdate = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state"), "free_video" => $request->input("free_video"), "video_path" => $request->input("video_path")
            ];
            if (!empty($request->input("file_path"))) {
                $dataUpdate['file_path'] = $request->input("file_path");
            }
            if (empty($existencia)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $existencia->update($dataUpdate);
                $message = "Lección actualizada con éxto";
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            return response()->json(["lecciones" => Lesson::all()], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Lesson::find($id);
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el Id:" . $id . " para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function addComment(Request $request, $id)
    {
        try {

            $insertado = Lesson_user_comment::create([
                "lesson_id" => $id, "user_id" => Auth::user()->id, "comment" => $request->input("comment"), "state" => $request->input("") ?? 1
            ]);
            if ($insertado)
                return response()->json(["message" => "Comentario insertado correctamente"], 200);
            else
                throw new Exception("Error en la inserción del comentario");
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function listComment($id)
    {
        try {

            $comments = Lesson_user_comment::join("users","users.id","=","lesson_user_comments.user_id")
            ->select(
                DB::raw("users.name as username")
                ,DB::raw("lesson_user_comments.comment")
                ,DB::raw("lesson_user_comments.created_at")
                )
            ->where("lesson_id",$id)->get();
            return response()->json(["comments" => $comments], 200);

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
