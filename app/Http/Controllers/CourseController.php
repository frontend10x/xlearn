<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Resource;
use App\Models\Skill;

use Exception;

class CourseController extends Controller
{
    public static function store(Request $request, $validate = true)
    {
        try {

            if($validate){
                $courses = Course::where("name", $request->input("name"))->first();
                if (!empty($courses)) {
                    throw new Exception("Ya existe un cursor con el nombre " . $request->input("name"));
                }
            }
            
            $dataInsert = [
                "name" => $request->input("name"), 
                "state" => $request->input("state"), 
                "free_video" => $request->input("free_video"), 
                "video_path" => $request->input("video_path"),
                "video_uri" => $request->input("video_uri"),
                "vimeo_id" => $request->input("vimeo_id"),
                "programs_id" => $request->input("program_id"),
                "area_id" => $request->input("area_id")
            ];
            if (!empty($request->input("file_path"))) {
                $dataInsert['file_path'] = $request->input("file_path");
            }

            if (!empty($request->input("description"))) {
                $dataInsert['description'] = $request->input("description");
            }

            $create_course = Course::create($dataInsert);
            return response()->json(["message" => "Curso creado con éxito", "id" => $create_course['id']]);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public static function sync_with_vimeo(Request $request)
    {
        try {

            $courses = Course::where("vimeo_id", $request->input("vimeo_id"))->first();

            if (empty($courses)) {
                $state = self::store($request, false);
            }else{
                $state = self::edit($request);
            }
            
            return json_encode($state->original);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public static function edit(Request $request)
    {
        try {

            $datosInsertar = Course::where("vimeo_id", $request->input("vimeo_id"))->first();

            $data = [
                "name" => $request->input("name"), 
                "state" => $request->input("state"), 
                "free_video" => $request->input("free_video"), 
                "video_path" => $request->input("video_path"),
                "video_uri" => $request->input("video_uri"),
                "vimeo_id" => $request->input("vimeo_id")
            ];

            if (!empty($request->input("area_id"))) {
                $data['area_id'] = $request->input("area_id");
            }

            if (!empty($request->input("description"))) {
                $data['description'] = $request->input("description");
            }

            if (!empty($request->input("file_path"))) {
                $data['file_path'] = $request->input("file_path");
            }

            if (empty($datosInsertar)) {
                throw new Exception("No existe el id: " . $request->input("vimeo_id") . " para ser actualizado");
            }

            $update_course = $datosInsertar->update($data);            

            return response()->json(["message" => "Curso actualizado con éxto", "id" => $datosInsertar->id], 200);
        
        } catch (Exception $e) {

            return return_exceptions($e);

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
            return return_exceptions($e);
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
            return return_exceptions($e);
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
            $limit = $request->has('limit') ? intval($request->get('limit')) : 'all';

            if($limit == 'all'){

                $consult = User::where('id', $userId)->with('courses')->get()->first();

            }else{
                
                $consult = User::where('id', $userId)->with('courses')->limit($limit)->offset(($offset - 1) * $limit)->get()->first();
            
            }
            
            
            if(empty($consult))
                throw new Exception("No se encontraron registros ");

            foreach ($consult->courses as $key => $value) {

                $request->merge(['user_id' => $userId, 'course_id' => $value['id']]);

                $progress = ProgressController::check_user_progress($request);

                $lessons = Lesson::where('course_id', $value['id'])->get('duration');
                // $total_video_time = $lessons->sum('duration');

                $total_video_time = LessonController::getTotalDuration([$value['id']]);

                $resources = $value['resources'] != null ? json_decode($value['resources'], true) : [];

                $progressPorcentage = progress($progress, $total_video_time);
                
                $courses[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'about_author' => $value['about_author'],
                    'img_author' => $value['img_author'],
                    'state' => $value['state'],
                    'vimeo_id' => $value['vimeo_id'],
                    'file_path' => $value['file_path'],
                    'video_uri' => $value['video_uri'],
                    'video_path' => $value['video_path'],
                    'resources' => Resource::select('name', 'type', 'description', 'file_path')->whereIn('id', $resources)->get(),
                    'lessons:amount' => count($lessons),
                    'progress:porcentage' => $progressPorcentage > 100 ? 100 : $progressPorcentage,
                ];
            }

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
                    "courses" => $courses
                )
            );

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
            return return_exceptions($e);
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
            return return_exceptions($e);
        }
    }

        /**
    * @OA\Get(
    *     path="/api/v1/course/show/{id}",
    *     summary="Mostrar curso por id",
    *     tags={"Courses"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="id", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los cursos del usuario.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "response": {
    *                           "_rel": "course",
    *                           "_embedded": {
    *                               "course": {
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
    public function show(Request $request, $id)
    {
        try {

            $course = Course::find($id);

            if (empty($course)) 
                throw new Exception("No existe el curso");

            $resources = $course->resources != null ? json_decode($course->resources, true) : [];
            $skills = $course->skills != null ? json_decode($course->skills, true) : [];

            $courses = [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'about_author' => $course->about_author,
                'img_author' => json_decode($course->img_author, true),
                'state' => $course->state,
                'vimeo_id' => $course->vimeo_id,
                'file_path' => $course->file_path,
                'video_uri' => $course->video_uri,
                'video_path' => $course->video_path,
                'resources' => Resource::select('name', 'type', 'description', 'file_path')->whereIn('id', $resources)->get(),
                'skills' => Skill::select('name')->whereIn('id', $skills)->get(),

            ];

            $course = array(
                "_rel"		=> "course",
                "_embedded" => array(
                    "course" => $courses
                )
            );

            return response()->json(["response" => $course], 200);
            
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}