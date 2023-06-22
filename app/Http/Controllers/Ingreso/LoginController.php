<?php

namespace App\Http\Controllers\Ingreso;

use Mail;
use Exception;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Controllers\companies\group\GroupController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
* @OA\Info(title="API's Xlearn", version="1.0")
*
* @OA\Server(url="https://servicios.10xconsultores.org")
* @OA\Server(url="https://127.0.0.1:8000")
*
* @OAS\SecurityScheme(
*      securityScheme="bearer_token",
*      type="http",
*      scheme="bearer"
* )
*/
class LoginController extends Controller
{
    /**
    * @OA\Post(
    *     path="/api/v1/login",
    *     tags={"Auth"},
    *     summary="Ingreso de usuarios",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="email", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="password", in="query", @OA\Schema(type="password")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Acceso correcto",
    *                       "token":"",
    *                       "datosUsuario":{
    *                           "id":0,
    *                           "name":"",
    *                           "email":"",
    *                           "phone":"",
    *                           "diagnostic":{
    *                               "status": false,
    *                           },
    *                           "roles":{
    *                               "id": 0,
    *                               "name": ""
    *                           },
    *                           "subcompanies_id":"",
    *                        }
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
    public function ingreso(Request $request)
    {
        try {
            
            if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'state' => 1])) {

                $user = User::where('email', $request->input('email'))->with('roles', 'typeUser', 'diagnostic')->first();
                
                $token = $user->createToken('token_jobs' . Auth::user()->id)->accessToken;
    
                if (!empty($request->input('token'))) {
                    $tokenCelular = $request->input('token');
                    //  $this->updateToken($tokenCelular, $user->id);
                }

                $diagnosticStatus = false;
    
                if($user->roles->rol_name === 'Lider'){
                    foreach ($user->diagnostic as $key => $value) {
                        if($value['confirmed'] === 1)
                            $diagnosticStatus = true;
                    }
                }
                    
                
                //Consultamos los grupos al que pertenece el usuario
                $user_groups = GroupController::listUserGroups($user->id);
                
                return response()->json(
                    [
                        'message' => "Acceso correcto", 
                        "token" => $token, 
                        "datosUsuario" => [
                            "id" => $user->id,
                            "name" => $user->name, 
                            "email" => $user->email, 
                            "phone" => $user->phone,
                            "groups" => $user_groups->original[0],
                            "roles" => [
                                "id" => $user->roles->id,
                                "name" => $user->roles->rol_name
                            ],
                            "diagnostic" => [
                                'status' => $diagnosticStatus
                            ],
                            "subcompanies_id" => $user->subcompanies_id
                        ]
                    ] 
                ,200);
            } else {
                return response()->json(["message" => "Datos incorrectos o usuario inactivo"],500);
            }

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }

        
    }

    // public function updateToken($token, $use_id)
    // {
    //     if (!empty($token) && !empty($use_id)) {
    //         $newToken = Usuario_token::where("user_id", $use_id)->first();
    //         if (empty($newToken)) {
    //             $newToken  = new Usuario_token();
    //             $newToken->user_id = $use_id;
    //         }
    //         $newToken->token = $token;
    //         $newToken->save();
    //     }
    // }
}