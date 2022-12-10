<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

use Exception;


class ProgressController extends Controller
{   
    /**
    * @OA\Post(
    *     path="/api/v1/progress/store",
    *     tags={"User course progress"},
    *     summary="Almacenar progreso de usuario en sus cursos",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="course_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="user_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="lesson_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="percentage", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="advanced_current_time", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="total_video_time", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro Almacenado/Actualizado con éxito"
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
    public static function progress_store(Request $request)
    {
        try {
            
            // Validamos los datos enviados
            $validated = $request->validate([
                'course_id' => 'required|integer',
                'user_id' => 'required|integer',
                'lesson_id' => 'required|integer',
                'percentage' => 'required',
                'advanced_current_time' => 'required',
                'total_video_time' => 'required'
            ]);

            $consult = Progress::where('course_id', $request->input("course_id"))
                            ->where('user_id', $request->input("user_id"))
                            ->where('lesson_id', $request->input("lesson_id"))->first();
            
            $status = 0;

            if( $request->input("percentage") >= 100 && ( $request->input("advanced_current_time") >= $request->input("total_video_time"))) 
                $status = 1;
            
            $data = [
                "course_id" => $request->input("course_id"), 
                "user_id" => $request->input("user_id"), 
                "lesson_id" => $request->input("lesson_id"), 
                "percentage_completion" => $request->input("percentage"), 
                "advanced_current_time" => $request->input("advanced_current_time"), 
                "total_video_time" => $request->input("total_video_time"),
                "status" => $status
            ];

            if (empty($consult))
                $query = self::store($data);
            else
                if($consult->percentage_completion < 100 ) $query = self::update($data, $consult);

            //Actualizar estado del curso del usuario
            $status_course = self::update_user_course( $request->input("course_id"), $request->input("user_id") );

            $message = "Video terminado con anterioridad";

            if (isset($query)) {
                $message = "Registro $query con éxito";
            }

            return response()->json(["message" =>  $message . $status_course], 200);
            
        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
        
    }

    public static function store($data)
    {
        try {

            Progress::create($data);

            return 'Almacenado';

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
        
    }

    public static function update($data, $consult)
    {
        try {

            $consult->update($data);

            return 'Actualizado';

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }

    }

    public static function update_user_course($course_id, $user_id)
    {
        try {

            $count_course_lessons = Lesson::where('course_id', $course_id)->count();

            $count_user_finished_lessons = Progress::where('course_id', $course_id)
                                                    ->where('user_id', $user_id)
                                                    ->where('status', 1)
                                                    ->count();
            
            if($count_course_lessons > $count_user_finished_lessons)
                $status = 'PROGRESS';
            else
                $status = 'COMPLETE';

            
            $update_status = DB::table('user_course')
                                ->where('course_id', $course_id)
                                ->where('user_id', $user_id)
                                ->update(['status' => $status]);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }

    }

    /**
    * @OA\Get(
    *     path="/api/v1/progress/user",
    *     tags={"User course progress"},
    *     summary="Consultar progreso de usuario",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="course_id", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="user_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "progress":"[]"
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
    public static function check_user_progress(Request $request, $users = false)
    {
        try {
            
            $consult = Progress::with('courses')->where('user_id', $request->input("user_id"))->get()->toArray();

            if($request->input("course_id")){

                $consult = Progress::with('courses')
                                    ->where('user_id', $request->input("user_id"))
                                    ->where('course_id', $request->input("course_id"))->get()->toArray();

            }

            if($users){
                $consult = Progress::with('courses')->whereIn('user_id', $users)->get()->toArray();
            }

            if(empty($consult))
                throw new Exception("No se encontró progreso");

            return response()->json(["progress" => $consult], 200);
 

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}
