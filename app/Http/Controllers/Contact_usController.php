<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Exception;
use Mail;
use App\Mail\EmailNotification;


use App\Models\Contact_us;

define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'));

class Contact_usController extends Controller
{   
    /**
    * @OA\Post(
    *     path="/api/v1/contact_us/store",
    *     tags={"Contact Us"},
    *     summary="Contacto - Soporte",
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="phone", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="company", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="observation", in="query", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro almacenado con éxito.",
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

            // Validamos los datos enviados
            $validated = $request->validate([
                'name' => 'required|string',
                'phone' => 'required|integer',
                'company' => 'required|string',
                'email' => 'required|string',
                'observation' => 'string',
            ]);

            $data = [
                "name" => $request->input("name"), 
                "phone" => $request->input("phone"), 
                "company" => $request->input("company"), 
                "email" => $request->input("email"), 
                "observation" => $request->input("observation")
            ];

            Contact_us::create($data);

            Mail::to(MAIL_FROM_ADDRESS)->send(new EmailNotification($data, 'contact_us'));
            
            return response()->json(["message" => "Solicitud enviada con éxito"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function index()
    {
        try {
            return response()->json(["contact_us" => Contact_us::all()], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}
