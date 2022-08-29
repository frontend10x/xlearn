<?php

namespace App\Http\Controllers\Ingreso;
//require '{path_to_root_folder}/autoload.php';

use Vimeo\Vimeo;
use Mail;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Controllers\companies\group\GroupController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

define("CLIENT_ID", env("CLIENT_ID_VIMEO"));
define("CLIENT_SECRET", env("CLIENT_SECRET_VIMEO"));
define("ACCESS_TOKEN", env("ACCESS_TOKEN_VIMEO"));

/**
* @OA\Info(title="API's Xlearn", version="1.0")
*
* @OA\Server(url="http://servicios.10xconsultores.org")
* @OA\Server(url="http://127.0.0.1:8000")
* @OA\Server(url="https://servicios.asstiseguridadsocial.com")
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

        // Validamos los datos enviados
       /*  $validated = $request->validate([
            'file' => 'required|mimes:mp4|max:8048',
        ]); */
        //$request->file->store('public/videos');
 
        //$request->file->store('public');

        //$client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);

            /*$file_name = '/Users/jairzeapaez/Downloads/test.mp4';
            $uri = $client->upload($file_name, array(
              "name" => "Test upload video",
              "description" => "The description goes here."
            ));
            "videos": {
                                "uri": "/users/177726805/videos",
                                "options": [
            echo "Your video URI is: " . $uri; */
    
      
       /* $response = $client->request('/me/projects', array(), 'GET');

        $arrayData = $response['body']['data'];

        $projects = [];
        $val = [];

        foreach ($arrayData as $key => $value) {

            $uri_video = $value['metadata']['connections']['videos']['uri'];
            $total_videos = $value['metadata']['connections']['videos']['total'];

            $project_name = $value['name'];

            $videos = $client->request($uri_video, array(), 'GET');
            $video_information = [];
            $val_video_information = [];

            $data_videos = $videos['body']['data'];

            $projects['name'] = $project_name;
            $projects['total_videos'] = $total_videos;
            
            foreach ($data_videos as $k => $item) {

                $video_information['player_embed_url'] = $item['player_embed_url'];
                $video_information['name'] = $item['name'];
                $video_information['description'] = $item['description'];
                $video_information['type'] = $item['type'];
                $video_information['duration'] = $item['duration'];
                $video_information['width'] = $item['width'];
                $video_information['pictures'] = $item['pictures']['base_link'];

                array_push($val_video_information, $video_information);

            }

            $projects['videos'] = $val_video_information;
           
            array_push($val, $projects);
        }

        return response()->json($val);*/

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'state' => 1])) {

            $user = User::where('email', $request->input('email'))->with('roles', 'typeUser', 'diagnostic')->first();
            
            $token = $user->createToken('token_jobs' . Auth::user()->id)->accessToken;

            if (!empty($request->input('token'))) {
                $tokenCelular = $request->input('token');
                //  $this->updateToken($tokenCelular, $user->id);
            }

            if($user->roles->rol_name === 'Lider' && $user->diagnostic[0]->confirmed === 1)
               $diagnosticStatus = true;
            else
                $diagnosticStatus = false;
            
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
