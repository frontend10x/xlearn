<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Question;
use Exception;

class EvaluationController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/v1/evaluation/course",
    *     summary="Mostrar evaluación del curso",
    *     tags={"Evaluations"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="course_id", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar evaluación del curso.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "response": {
    *                           "_rel": "evaluations",
    *                           "_embedded": {
    *                               "evaluations": {}
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
    public function showCourse(Request $request)
    {
        try {

            // Validamos los datos enviados
            $validated = $request->validate([
                'course_id' => 'required|integer|exists:evaluations'
            ]);

            $evaluation = Evaluation::where('course_id', $request->get("course_id"))
                                    ->select('id', 'questions', 'average_score', 'Attempts', 'course_id')
                                    ->first();
            
            $id_quest = json_decode($evaluation->questions);

            $questions = Question::whereIn('id', $id_quest)
                                 ->select('id', 'question', 'required', 'type', 'response_types', 'answer')
                                 ->with('options')
                                 ->get()->toArray();

            
            $response = array(
                "_rel"		=> "evaluation",
                "_embedded" => array(
                    "evaluation" => [
                        'id' => $evaluation->id,
                        'average_score' => $evaluation->average_score,
                        'Attempts' => $evaluation->Attempts,
                        'questions' => $questions

                    ]
                    
                )
            );

            return response()->json(["response" => $response], 200);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}
