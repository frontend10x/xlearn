<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
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

    /**
    * @OA\Get(
    *     path="/api/v1/course/list",
    *     summary="Mostrar cursos",
    *     tags={"Courses"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los cursos.",
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
    *                                       "updated_at": "2022-06-12T00:46:06.000000Z"
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
    public function index(Request $request)
    {
        try {

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

            $consult = Course::where('state', 1)->with('areas')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            $course = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => Course::count('state', 1), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "courses",
                "_embedded" => array(
                    "courses" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron registros");

            return response()->json(["response" => $course], 200);
            
            //return response()->json(["cursos" => Course::all()], 200);

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/course/show_area/{areaId}",
    *     summary="Mostrar curso por areas",
    *     tags={"Courses"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="areaId", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los cursos del area.",
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
    public function show_area(Request $request, $areaId)
    {
        try {

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

            $consult = Course::where('area_id', $areaId)->with('areas')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            $course = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => Course::count('area_id', $areaId), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "courses",
                "_embedded" => array(
                    "courses" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron registros");

            return response()->json(["response" => $course], 200);
            
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/course/show_user/{userId}",
    *     summary="Mostrar curso por usuario",
    *     tags={"Courses"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="userId", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los cursos del usuario.",
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
    *                           "_rel": "courses",
    *                           "_embedded": {
    *                               "courses": {
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
    public function show_user(Request $request, $userId)
    {
        try {

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;
            
            $consult = User::where('id', $userId)->with('courses')->limit($limit)->offset(($offset - 1) * $limit)->get()->first();

            $total = User::where('id', $userId)->with('courses')->first();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            $course = array(
                "hc:length" => count($consult->courses), //Es la longitud del array a devolver
                "hc:total"  => count($total->courses), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "courses",
                "_embedded" => array(
                    "courses" => $consult->courses
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron registros");

            return response()->json(["response" => $course], 200);
            
        } catch (Exception $e) {
            return return_exceptions($e);
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
