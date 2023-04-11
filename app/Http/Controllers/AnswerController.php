<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use Exception;

class AnswerController extends Controller
{   
    /**
    * @OA\Post(
    *     path="/api/v1/answer/store",
    *     tags={"Answers"},
    *     summary="Crear respuestas de usuario",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="evaluation_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="user_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="course_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="answers", required=true, in="query", @OA\Schema(type="[]")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Respuestas almacenadas con éxito.",
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
    public function store(Request $request)
    {
        try {
            
            $validate = $request->validate([
                'evaluation_id' => 'required|integer|exists:evaluations,id',
                'user_id' => 'required|integer|exists:users,id',
                'course_id' => 'required|integer|exists:courses,id',
                'answers' => 'required',
            ]);

            $consult = Answer::where('user_id', $request->input("user_id"))
                                ->where('evaluation_id', $request->input("evaluation_id"))
                                ->where('course_id', $request->input("course_id"))->update(['valid' => 0]);

            foreach ($request->input("answers") as $key => $value) {
                Answer::create([
                    'evaluation_id' => $request->input("evaluation_id"),
                    'question_id' => $value["question_id"],
                    'user_id' => $request->input("user_id"),
                    'course_id' => $request->input("course_id"),
                    'answer' => $value["answer"],
                    'valid' => 1
                ]);
            }

            return json_encode(["message" => "Respuestas almacenadas con éxito"]);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}