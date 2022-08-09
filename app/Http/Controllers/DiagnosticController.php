<?php

namespace App\Http\Controllers;
use App\Models\Diagnostic;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    /**
    * @OA\Post(
    *     path="/api/v1/diagnostic/store",
    *     tags={"Diagnostic"},
    *     summary="Almacenar diagnostico del lider",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="target", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="user_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="_rel", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="answers", required=true, in="query", @OA\Schema(type="[]")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro almacenado con éxito.",
    *                      "course_route":"[]"
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
    public static function store(Request $request)
    {

        try {

            // Validamos los datos enviados
            $validated = $request->validate([
                'target' => 'required',
                'user_id' => 'required|integer',
                '_rel' => 'required',
                'answers' => 'required'
            ]);

            $dataInsert = [
                "target" => $request->input("target"), 
                "user_id" => $request->input("user_id"), 
                "rel" => $request->input("_rel"), 
                "answers" => json_encode($request->input("answers"))
            ];

            $courses_to_consult = [];
            
            foreach ($request->input("answers") as $key => $value) {
                array_push($courses_to_consult, $value['course']);
            }


            $toCreate = Diagnostic::create($dataInsert);
            $courses = Course::where('state', 1)->whereIn('id', $courses_to_consult)->get();

            //Mail::to($request->input("email"))->send(new ConfirmationRegisterRequest($encryptedId));

            return json_encode([
                "message" => "Registro almacenado con éxito", 
                "diagnostic_id" => $toCreate['id'], 
                "course_route" => $courses
            ]);

        } catch (Exception $e) {

            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine()], 500);
            \Log::debug('message ' . $e->getMessage());

        }
    }

    /**
    * @OA\Patch(
    *     path="/api/v1/diagnostic/confirm_route/{diagnostic_id}",
    *     tags={"Diagnostic"},
    *     summary="Confirmar diagnostico",
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Ruta confirmada correctamente"
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
    public static function confirm_route($diagnostic_id)
    {
        try {
            
            if (empty($diagnostic_id))
                throw new Exception("No existe id de diagnostico para actualizar");

            $search = Diagnostic::find($diagnostic_id);
            $search->update(['confirmed' => 1]);
            
            return response()->json(["message" => 'Ruta confirmada correctamente'], 200);


        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine()], 500);
        }
    }
}
