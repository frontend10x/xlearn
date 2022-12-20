<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Exception;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Mail\EmailNotification;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

define('ROLE_NAME', 'Integrante');

class UserController extends Controller
{

    public function datauser($id)
    {
        try {

            $user = User::find($id);
            if (empty($user))
                throw new Exception("No existe usuario con el id: " . $id);

            return response()->json(["user" => $user], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/user/store",
    *     tags={"Users"},
    *     summary="Crear usuarios",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="area", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="rol_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="link_facebook", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_google", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_linkedin", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_instagram", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="surname", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="phone", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="subcompanies_id", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="password", in="query", @OA\Schema(type="password")),
    *     @OA\Parameter(name="password_confirmation", in="query", @OA\Schema(type="password")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro almacenado con éxito.",
    *                      "id":0,
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
            

            $consult = User::where("email", $request->input("email"))->first();

            if (!empty($consult)) {
                throw new Exception("El usuario ya se encuentra registrado");
            }

            if ( $request->input("password") != $request->input("password_confirmation")) {
                throw new Exception("Las contraseñas no coinciden");
            }

            // Validamos los datos enviados
            $validated = $request->validate([
                'password' => 'required',
                'password_confirmation' => 'required',
                'email' => 'required',
                'subcompanies_id' => 'required|integer',
                'name' => 'required', 
                'area' => 'required',   
            ]);

            $request->request->add(['subcompanie_id' => $request->subcompanies_id]);

            $isRegistersRequest = $request->input("registerRequest");

            if(empty($isRegistersRequest)){

                $quotas = PaymentController::approvedPaymentStatus($request);

                if(!isset($quotas->original['quotas']) || !$quotas->original['quotas'])
                    throw new Exception('La empresa no tiene cupos para registrar usuarios');
            }

            $dataInsert = [
                "subcompanies_id" => $request->input("subcompanies_id"), "area" => $request->input("area"),
                "link_facebook" => $request->input("link_facebook"), "link_google" => $request->input("link_google"), "link_linkedin" => $request->input("link_linkedin"), "link_instagram" => $request->input("link_instagram"), "name" => $request->input("name"), "surname" => $request->input("surname"), "phone" => $request->input("phone"), "email" => $request->input("email"), "state" => 0, "password" => Hash::make($request->input("password"))
            ];

            if (empty($request->input("rol_id"))) {

                $rolId = RolesController::showIdByName(ROLE_NAME);

                if(empty($rolId))
                    throw new Exception('No se encontro un rol para '.ROLE_NAME.', por favor comuniquese con el administrador del sistema.');

                $dataInsert['rol_id'] = $rolId;
            }

            $userCreated = User::create($dataInsert);

            $encryptedId = Crypt::encryptString($userCreated['id']);

            Mail::to($request->input("email"))->send(new EmailNotification($encryptedId, 'confirmation_register'));

            return json_encode(["message" => "Registro almacenado con éxito", "id" => $userCreated['id']]);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    /**
    * @OA\Put(
    *     path="/api/v1/user/edit/{id}",
    *     tags={"Users"},
    *     summary="Editar usuarios",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="area", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="rol_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="link_facebook", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_google", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_linkedin", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="link_instagram", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="surname", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="phone", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="subcompanies_id", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="password", in="query", @OA\Schema(type="password")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Usuario actualizado con éxto.",
    *                      "id":0,
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
    public function edit(Request $request, $id)
    {
        try {
            $buscaActualiza = User::find($id);
            $dataUpdate = [
                "area" => $request->input("area"), "rol_id" => $request->input("rol_id"), "link_facebook" => $request->input("link_facebook"), "link_google" => $request->input("link_google"), "link_linkedin" => $request->input("link_linkedin"), "link_instagram" => $request->input("link_instagram"), "name" => $request->input("name"), "surname" => $request->input("surname"), "phone" => $request->input("phone"), "email" => $request->input("email"), "state" => $request->input("state")
            ];
            // echo $request->input("subcompanies_id");die;
            $dataUpdate['subcompanies_id'] = !empty($request->input("subcompanies_id")) ? $request->input("subcompanies_id") : null;
            if (!empty($request->input("password"))) {
                $dataUpdate['password'] = Hash::make($request->input("password"));
            }
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $buscaActualiza->update($dataUpdate);
                $message = "Usuario actualizado con éxto";
            }
            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/user/list",
    *     summary="Mostrar usuarios",
    *     tags={"Users"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios.",
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
    *                           "_rel": "users",
    *                           "_embedded": {
    *                               "users": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "lastname": "",
    *                                   "company": "",
    *                                   "email": "",
    *                                   "website": "",
    *                                   "size": 0,
    *                                   "country_id": 0,
    *                                   "content": "",
    *                                   "plan_id": 0,
    *                                   "quotas": 0,
    *                                   "observation": "",
    *                                   "created_at": "2022-06-11T23:21:42.000000Z",
    *                                   "updated_at": "2022-06-12T00:46:06.000000Z",
    *                                   "countries": {
    *                                       "id": 0,
    *                                       "name": ""
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
            if (!empty(Auth::user()->subcompanies_id)) {
                $consult = User::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            } 

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

            $consult = User::with('roles')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;
            

            $users = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => User::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "users",
                "_embedded" => array(
                    "users" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron usuarios");

            return response()->json(["response" => $users], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function changestate(Request $request, $id)
    {
        try {

            $desencryptedId = Crypt::decryptString($id);

            $buscaActualiza = User::find($desencryptedId);
            if (empty($buscaActualiza)) {
                throw new Exception("Ocurrio un error");
            }
            /*.json_encode($buscaActualiza)*/
            $buscaActualiza->update(["state" => 1]);
            
            header("Location:" . env('URL_FRONT') . "/login ", TRUE, 301);
            exit();
            
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/user/forgot_password",
    *     tags={"Users"},
    *     summary="Olvido su contraseña",
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Contraseña modificada con éxito.",
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
    public function forgot_password(Request $request)
    {
        try {

            $user = User::where('email', $request->input("email"))->first();

            if (empty($user))
                throw new Exception("No existe email registrado");

            $encryptedId = Crypt::encryptString($user->id);
            
            Mail::to($request->input("email"))->send(new EmailNotification($encryptedId, 'forgot_password'));

            return response()->json(["message" => "Se ha enviado un correo para la recuperación de la contraseña"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/v1/user/recover_password/{id}",
    *     tags={"Users"},
    *     summary="recuperar contraseña",
    *     @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="string")),
    *     @OA\Parameter(name="password", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="password_confirmation", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Contraseña modificada con éxito.",
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
    public function recover_password(Request $request, $id)
    {
        try {

            $desencryptedId = Crypt::decryptString($id);

            $user = User::find($desencryptedId);

            if (empty($user))
                throw new Exception("No existe usuario registrado");

            if ( $request->input("password") != $request->input("password_confirmation")) 
                throw new Exception("Las contraseñas no coinciden");
            

            $user->password =  Hash::make($request->input("password"));
            $user->save();

            return response()->json(["message" => "Contraseña modificada con éxito"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/v1/user/change_password",
    *     tags={"Users"},
    *     security={{"bearer_token":{}}},
    *     summary="Cambiar contraseña",
    *     @OA\Parameter(name="password", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="old_password", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Contraseña modificada con éxito.",
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
    public function change_password(Request $request)
    {
        try {

            $credentials = [
                'password' => $request->input('old_password'),
                'id' =>  Auth::user()->id
            ];

            $token = Auth::guard('api')->attempt($credentials);

            return $token;

            if (Auth::attempt(['id' => Auth::user()->id, 'password' => $request->input('old_password')])) {

                $user = User::find(Auth::user()->id);
                $user->password =  Hash::make($request->input("password"));
                $user->save();

                return response()->json(["message" => "Contraseña modificada con éxito"], 200);

            } else {

                return response()->json(["message" => "Datos incorrectos"],500);

            }

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function coursesFavorites(Request $request)
    {
        try {

            $course = Course::find($request->input("course_id"));
            if (empty($course))
                throw new Exception("El Id del curso no existe");

            $user = User::find(Auth::user()->id);

            $existencia = DB::table("user_course_favorite")->where("course_id",$request->input("course_id"))->first();
            if(empty($existencia)){

                $user->coursesFavorites()->attach($request->input("course_id"));
                return response()->json(["message" => "Curso favorito almacenado con éxito"], 200);
            }       else{
                return response()->json(["message" => "Curso ya se encuentra registrado como favorito"], 200);
            }

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/user/bulk_upload_users",
    *     tags={"Users"},
    *     summary="Carga masiva de usuarios",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="file", required=true, in="query", @OA\Schema(type="file")),
    *     @OA\Parameter(name="subcompanies_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Usuarios cargados correctamente"
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
    public function bulkUploadUsers(Request $request)
    {

        try {
            
            Excel::import(new UsersImport($request->subcompanies_id), $request->file);

            return response()->json(["message" => "Usuarios cargados correctamente"], 200);

        } catch (Exception $e) {
            
            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine()], 500);

        }
         
    }

    /**
    * @OA\Get(
    *     path="/api/v1/user/sub_companies_withou_group",
    *     summary="Mostrar usuarios de la empresa sin grupo",
    *     tags={"Users"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="subcompanies_id", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios de la empresa sin grupo.",
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
    *                           "_rel": "users",
    *                           "_embedded": {
    *                               "users": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "lastname": "",
    *                                   "company": "",
    *                                   "email": "",
    *                                   "website": "",
    *                                   "size": 0,
    *                                   "country_id": 0,
    *                                   "content": "",
    *                                   "plan_id": 0,
    *                                   "quotas": 0,
    *                                   "observation": "",
    *                                   "created_at": "2022-06-11T23:21:42.000000Z",
    *                                   "updated_at": "2022-06-12T00:46:06.000000Z",
    *                                   "sub_companies": {}
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
    public function showUserWithoutGroup(Request $request)
    {
        try {

            // Validamos los datos enviados
            $validated = $request->validate([
                'subcompanies_id' => 'required|integer'
            ]);

            if (!empty(Auth::user()->subcompanies_id)) {
                $consult = User::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            }

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 'all';

            if($limit != 'all' && $limit > 0)
                $consult = User::with('subCompanies')
                                ->where('subcompanies_id', $request->get('subcompanies_id'))
                                ->where('group_id', NULL)
                                ->limit($limit)->offset(($offset - 1) * $limit)
                                ->get()->toArray();
            else
                $consult = User::with('subCompanies')
                                ->where('subcompanies_id', $request->get('subcompanies_id'))
                                ->where('group_id', NULL)
                                ->get()->toArray();
            
            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            if(empty($consult))
                throw new Exception("No se encontraron usuarios");

            $users = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => User::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "users",
                "_embedded" => array(
                    "users" => $consult
                )
            );

            return response()->json(["response" => $users], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/sub_companies/users/{id}",
    *     summary="Mostrar usuarios de la empresa",
    *     tags={"Sub Companies"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="id", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios de la empresa.",
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
    *                           "_rel": "users",
    *                           "_embedded": {
    *                               "users": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "lastname": "",
    *                                   "company": "",
    *                                   "email": "",
    *                                   "website": "",
    *                                   "size": 0,
    *                                   "country_id": 0,
    *                                   "content": "",
    *                                   "plan_id": 0,
    *                                   "quotas": 0,
    *                                   "observation": "",
    *                                   "created_at": "2022-06-11T23:21:42.000000Z",
    *                                   "updated_at": "2022-06-12T00:46:06.000000Z",
    *                                   "sub_companies": {}
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
    public static function showUserSubCompanie(Request $request, $id)
    {
        try {

            
            if (!empty(Auth::user()->subcompanies_id)) {
                $consult = User::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            }

            //TODO debe sacarse del request, por defecto el valor es uno
            $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

            //TODO debe sacarse del request, por defecto el valor es 10.
            $limit = $request->has('limit') ? intval($request->get('limit')) : 'all';

            if($limit != 'all' && $limit > 0)
                $consult = User::with('subCompanies')
                                ->where('subcompanies_id', $id)
                                ->limit($limit)->offset(($offset - 1) * $limit)
                                ->get()->toArray();
            else
                $consult = User::with('subCompanies')
                                ->where('subcompanies_id', $id)
                                ->get()->toArray();
            
            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            if(empty($consult))
                throw new Exception("No se encontraron usuarios");

            $users = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => User::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "users",
                "_embedded" => array(
                    "users" => $consult
                )
            );

            return response()->json(["response" => $users], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}
