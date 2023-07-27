<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Evaluation;
use App\Models\Question;
use App\Imports\EvaluationImport;

use Exception;

class EvaluationController extends Controller
{

    public function store(Request $request)
    {
        try {
            
            $validate = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
                'questions' => 'required',
                'average_score' => 'required|integer',
                'Attempts' => 'required|integer',
            ]);
            
            Evaluation::create([
                'questions' => $request->input("questions"),
                'average_score' => $request->input("average_score"),
                'Attempts' => $request->input("Attempts"),
                'course_id' => $request->input("course_id"),
                
            ]);
        
            return json_encode(["message" => "Evaluación almacenada con éxito"]);
        } catch (Exception $e) {
            
            return return_exceptions($e);
        }
}
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

    /**
    * @OA\Post(
    *     path="/api/v1/evaluation/bulk_upload_evaluation",
    *     tags={"Evaluations"},
    *     summary="Carga masiva de evaluaciones",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="file", required=true, in="query", @OA\Schema(type="file")),
    *     @OA\Parameter(name="course_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Evaluación cargada correctamente"
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
    public function bulkUploadEvaluation(Request $request)
    {
        try {

            $importer = new EvaluationImport($request->course_id);
            
            Excel::import($importer, $request->file);

            $importedRows = $importer->getImportedRows();

            $questionsIds = get_ids($importedRows['Preguntas']->questions, 'id'); 

            $request->request->add([
                'questions' => json_encode($questionsIds),
                'average_score' => 80,
                'Attempts' => 2,
                'course_id' => $request->course_id
            ]);

            $evaluation = $this->store($request);
            
            return response()->json(["message" => "Evaluación cargada correctamente"], 200);
            
        } catch (Exception $e) {
            
            return return_exceptions($e);
            
        }
        
    }
}