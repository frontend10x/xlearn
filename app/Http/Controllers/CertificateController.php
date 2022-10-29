<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Question;


class CertificateController extends Controller
{
    public function show(Request $request)
    {
        try {
            
            $validate = $request->validate([
                'user_id' => 'required|integer',
                'course_id' => 'required|integer',
            ]);

            $userResponses = Answer::where('user_id', $request->get("user_id"))
                                    ->where('course_id', $request->get("course_id"))
                                    ->select('answer', 'question_id')
                                    ->get()->toArray();

            if(empty($userResponses))
                throw new Exception("No se encontraron registros");
            
            $correctAnswer = $this->getCorrectAnswers($request->get("course_id"));

            $pointsInFavor = 0;

            foreach ($correctAnswer as $key => $correct) {
                
                foreach ($userResponses as $userResponse) {

                    if($correct['id'] === $userResponse['question_id']){

                        if ($correct['answer'] === $userResponse['answer']) {
                            
                            $pointsInFavor++;

                        }
                    }

                };
            };

            return response()->json(["questions" => $key, "answer" => $pointsInFavor], 200);

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
        
        return $questions;
    }
}
