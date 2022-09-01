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
    public static function store(Request $request)
    {
        try {
            $lesson = Lesson::where("name", $request->input("name"))->first();
            if (!empty($lesson)) {
                throw new Exception("Ya existe una lección con el nombre " . $request->input("name"));
            }
            $datosInsert = [
                "name" => $request->input("name"), 
                "description" => $request->input("description"), 
                "state" => $request->input("state"), 
                "free_video" => $request->input("free_video"), 
                "video_path" => $request->input("video_path"),
                "course_id" => $request->input("course_id"),
                "vimeo_id" => $request->input("vimeo_id"),
                "player_embed_url" => $request->input("player_embed_url"),
                "picture" => $request->input("picture")
            ];
            if (!empty($request->input("file_path"))) {
                $datosInsert['file_path'] = $request->input("file_path");
            }

            Lesson::create($datosInsert);
            return response()->json(["message" => "Leccion creada con éxito", "status" => true], 200);

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


    /**
    * @OA\Get(
    *     path="/api/v1/lesson/show_course/{courseId}",
    *     summary="Mostrar lecciones por cursos",
    *     tags={"Lesson"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="courseId", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos las lecciones del curso.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "response": {
    *                           "hc:length": 0,
    *                           "hc:total": 0,
    *                           "hc:offset": 0,
    *                           "hc:limit": 0,
    *                           "hc:next": "next page end-point ",
    *                           "hc:previous": "previous page end-point ",
    *                           "_rel": "course",
    *                           "_embedded": {
    *                               "course": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "description": "",
    *                                   "state": 0,
    *                                   "free_video": "",
    *                                   "file_path": "",
    *                                   "video_path": "",
    *                                   "area_id": 0,
    *                                   "programs_id": 0,
    *                                   "created_at": "2022-06-11T23:21:42.000000Z",
    *                                   "updated_at": "2022-06-12T00:46:06.000000Z",
    *                                   "areas": {
    *                                       "id": 0,
    *                                       "name": "",
    *                                       "description": "",
    *                                       "file_path": "",
    *                                       "state": 0,
    *                                       "created_at": "2022-06-11T23:21:42.000000Z",
    *                                       "updated_at": "2022-06-11T23:21:42.000000Z"
    *                                       }
    *                                   }
    *                               }
    *                           }
    *                       }
    *                 },
    *             ),
    * 
    *         ),
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Failed",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Mensaje de error",
    *                 },
    *             ),
    * 
    *         ),
    *     )
    * )
    */
    public function show_course(Request $request, $courseId)
    {
        try {

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

            $consult = Lesson::where('course_id', $courseId)->with('courses')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            $lessons = Lesson::where('course_id', $courseId)->get();

            $lesson = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => $lessons->count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "lesson",
                "_embedded" => array(
                    "lesson" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron registros");

            return response()->json(["response" => $lesson], 200);
            
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
