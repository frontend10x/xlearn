<?php

namespace App\Http\Controllers;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/v1/questions/list",
    *     summary="Mostrar preguntas para diagnostico",
    *     tags={"Questions"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos las preguntas.",
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
    *                           "_rel": "questions",
    *                           "_embedded": {
    *                               "questions": {
    *                                   {
    *                                   "id": 0,
    *                                   "question": "",
    *                                   "required": true,
    *                                   "options": {
    *                                       "id": 0,
    *                                       "response": ""
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

            $consult = Question::select('id', 'question', 'required', 'type', 'response_types', 'answer')->with('options')->where('type', 'diagnostico')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;
            
            $response = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => Question::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "questions",
                "_embedded" => array(
                    "questions" => $consult
                    
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron preguntas");

            return response()->json(["response" => $response], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/questions/list_faqs",
    *     summary="Mostrar preguntas frecuentes",
    *     tags={"Questions"},
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos las preguntas frecuentes.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "response": {
    *                           "_rel": "faqs",
    *                           "_embedded": {
    *                               "faqs": {}
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
    public function faqs(Request $request)
    {
        try {

            $consult = Question::where('type', 'faqs')->with('options')->get()->toArray();

            $questions = array(
                "_rel"		=> "faqs",
                "_embedded" => array(
                    "faqs" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron registros");

            return response()->json(["response" => $questions], 200);

        } catch (Exception $e) {

            return return_exceptions($e);

        }
    }

}
