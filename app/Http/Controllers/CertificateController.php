<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;

define('PATH_BASE', env('PATH_BASE'));

class CertificateController extends Controller
{   
    /**
    * @OA\Post(
    *     path="/api/v1/certificate/generate",
    *     tags={"Certificate"},
    *     summary="Generar certificado",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="user_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="course_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "code":"UuuId",
    *                      "path":"path/code",
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
    public function generate(Request $request)
    {
        
        try {
            
            
            $validate = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $user_id = $request->input("user_id");
            $course_id = $request->input("course_id");

            $consultCertificate = Certificate::where('user_id', $user_id)
                                              ->where('course_id', $course_id)->first();

            if (!empty($consultCertificate)) {
                return response()->json(["status" => true, "code" => $consultCertificate->code, "paths" => json_decode($consultCertificate->path), "results" => json_decode($consultCertificate->results)], 200);
            }

            $userResponses = Answer::where('user_id', $user_id)
                                    ->where('course_id', $course_id)
                                    ->select('answer', 'question_id', 'updated_at')
                                    ->get()->toArray();
            

            if(empty($userResponses))
                throw new Exception("No se encontraron registros");
            
            $pointsInFavor = 0;
            $finishDate = date('Y-m-d h:mm;ss');
            $results = [];

            $correctAnswer = $this->getCorrectAnswers($course_id);

            foreach ($correctAnswer['questions'] as $key => $correct) {

                $index = array_search($correct['id'], array_column($userResponses, 'question_id'));                

                array_push($results, [
                    "question_id" => $correct['id'],
                    "answers" => [
                        "user" => $userResponses[$index]['answer'],
                        "correct" => $correct['answer']
                    ]
                ]);

            };

            $finishDate = $userResponses[count($userResponses) - 1]['updated_at'];

            $pointsInFavor = self::calculate_percentage($results, $pointsInFavor);

            $percentage = round($pointsInFavor / $key * 100);

            if($percentage >= $correctAnswer['average_score']){

                $consultCertificate = $this->store($user_id, $course_id, $percentage, $results, $finishDate);

            }else{

                return response()->json([
                    "status" => false, 
                    "message" => "Lo sentimos, su evaluación no fue aprobada.",
                    "percentage" => $percentage
                ], 200);

            }

            return response()->json([
                "status" => true, 
                "code" => $consultCertificate->code, 
                "paths" => json_decode($consultCertificate->path), 
                "results" => json_decode($consultCertificate->results)
            ], 200);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public static function calculate_percentage($results, $pointsInFavor)
    {
        try {
            
            foreach ($results as $result) {

                if ($result['answers']['user'] === $result['answers']['correct']) {

                    $pointsInFavor++;

                }

            }

            return  $pointsInFavor;

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function store($user_id, $course_id, $percentage, $correctAnswers, $finish_date)
    {
        try {

            $user = User::find($user_id);
            $course = Course::find($course_id);
            $code = Str::uuid();
            $showPath = PATH_BASE . '/certificate/show/' . $code;
            $showDownload = PATH_BASE . '/certificate/download/' . $code;
            
            $consultCertificate = Certificate::create([
                'code' => $code,
                'user_name' => $user->name,
                'user_id' => $user_id,
                'course_name' => $course->name,
                'course_id' => $course_id,
                'results' => json_encode([
                    "percentage" => $percentage,
                    "correct_answers" => $correctAnswers
                ]),
                'path' => json_encode([
                    "show" => $showPath,
                    "download" => $showDownload
                ]),
                'finish_date' => date('Y-m-d h:m:s', strtotime($finish_date))
            ]);

            return $consultCertificate;

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function index(Reques $request)
    {
        try {
            //code...
        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function getCorrectAnswers($courseId)
    {
        $evaluation = Evaluation::where('course_id', $courseId)
                                ->select('questions', 'average_score')
                                ->first();
        
        $id_quest = json_decode($evaluation->questions);

        $questions = Question::whereIn('id', $id_quest)
                             ->select('answer', 'id')
                             ->get()->toArray();
        
        return [
            'questions' => $questions,
            'average_score' => $evaluation->average_score
        ];
    }

    /**
    * @OA\Get(
    *     path="/api/v1/certificate/download/{code}",
    *     tags={"Certificate"},
    *     summary="Descargar certificado",
    *     @OA\Parameter(name="code", required=true, in="path", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/pdf",
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
    public function dowmload($code)
    {
        try {
            
            $pdf = $this->createCertificatePDF($code);
                    
            return $pdf->download('certificado.pdf');

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/certificate/show/{code}",
    *     tags={"Certificate"},
    *     summary="Visualizar certificado",
    *     @OA\Parameter(name="code", required=true, in="path", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/pdf",
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
    public function show($code)
    {
        try {

            $pdf = $this->createCertificatePDF($code);
                    
            return $pdf->stream('certificado.pdf');

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function createCertificatePDF($code)
    {
        try {
            
            $consult = Certificate::where('code', $code)->first();

            $data = [
                'user' => $consult->user_name,
                'course' => $consult->course_name,
                'date' => date('d/m/Y', strtotime($consult->finish_date) ) 
            ];
            
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('certificate', $data)->setPaper('a4', 'landscape');
        
            return $pdf;

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}
