<?php

namespace App\Http\Controllers;

use Exception;
use Mail;
use App\Http\Controllers\UserController;
use App\Models\RegistrationRequest;
use App\Models\Sub_companies;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\EmailNotification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;

class RegisterRequestController extends Controller
{

    /**
    * @OA\Get(
    *     path="/api/v1/register_requests/list",
    *     summary="Mostrar solicitudes de registro",
    *     tags={"Register request"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todas los solicitudes."
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
            
            $consult = RegistrationRequest::with('countries')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

            $nexOffset = $offset + 1;
            $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            $requests = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => RegistrationRequest::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "requests",
                "_embedded" => array(
                    "requests" => $consult
                )
            );

            if(empty($consult))
                throw new Exception("No se encontraron solicitudes de registro");

            return response()->json(["response" => $requests], 200);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/register_requests/store",
    *     tags={"Register request"},
    *     summary="Crear solicitudes de registro",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="lastname", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="company", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="website", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="size", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="country", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="content", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="plan_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="quotas", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="observation", in="query", @OA\Schema(type="string")),
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
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'password_confirmation' => 'required',
                'country' => 'required|integer',
                'content' => 'required|integer',
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|confirmed|unique:users|unique:registration_requests',
                'email_confirmation' => 'required|email',
                'size' => 'required',
                'plan_id' => 'required|integer',
                'quotas' => 'required|integer',
                'company' => 'required|unique:sub_companies,name',
                'nit' => 'required|unique:sub_companies',
                'phone' => 'required',
            ]);

            // Creamos el array para insertar
            $dataInsert = [
                "name" => $request->input("name"), 
                "lastname" => $request->input("lastname"), 
                "company" => $request->input("company"), 
                "email" => $request->input("email"), 
                "website" => $request->input("website"), 
                "size" => $request->input("size"), 
                "country_id" => $request->input("country"), 
                "content" => $request->input("content"), 
                "plan_id" => $request->input("plan_id"), 
                "quotas" => $request->input("quotas"),
                "observation" => $request->input("observation")
            ];

            $register = RegistrationRequest::create($dataInsert);

            $dataInsert["password"] = $request->input("password");
            $dataInsert["password_confirmation"] = $request->input("password_confirmation");
            $dataInsert["rol_id"] = 3;
            $dataInsert["type_id"] = 2;
            $dataInsert["state"] = 0;
            $dataInsert["link_facebook"] = '';
            $dataInsert["link_google"] = '';
            $dataInsert["link_linkedin"] = '';
            $dataInsert["link_instagram"] = '';
            $dataInsert["surname"] = '';
            $dataInsert["registerRequest"] = true;

            if(!empty($register)){

                $companyCreated = SubcompaniesController::store($request);

                $dataInsert["subcompanies_id"] = json_decode($companyCreated, true)['id'];

                $request->request->add(array_merge($dataInsert, $request->all()));

                $userCreated = UserController::store($request);
                // $userId = json_decode($userCreated, true);

                // //Encriptamos el id del usuario
                // $encryptedId = Crypt::encryptString($userId['id']);

            }

            return response()->json(["message" => "Registro realizado con éxito. Revisa tu correo para confirma la cuenta. Ve a la casilla de correos no deseados en caso de no encontrarlo en tu bandeja de entrada"], 200);

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()], 500);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/v1/register_requests/edit/{{id}}",
    *     tags={"Register request"},
    *     summary="Actualizar solicitudes de registro",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="lastname", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="company", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="website", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="size", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="country", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="content", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="plan_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="quotas", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="observation", in="query", @OA\Schema(type="string")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro actualizado con éxito.",
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

            $buscaActualiza = RegistrationRequest::find($id);

            if (empty($buscaActualiza))
                throw new Exception("la solicitud de registro con id: " . $id . " no existe");

            // Validamos los datos enviados
            $validated = $request->validate([
                'country' => 'required|integer',
                'content' => 'required|integer',
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'required',
                'website' => 'required',
                'size' => 'required',
                'plan_id' => 'required|integer',
                'quotas' => 'required|integer',
                'company' => 'required'
            ]);

            // Creamos el array para actualizar
            $data = [
                "name" => $request->input("name"), 
                "lastname" => $request->input("lastname"), 
                "company" => $request->input("company"), 
                "email" => $request->input("email"), 
                "website" => $request->input("website"), 
                "size" => $request->input("size"), 
                "country_id" => $request->input("country"), 
                "content" => $request->input("content"), 
                "plan_id" => $request->input("plan_id"), 
                "quotas" => $request->input("quotas"), 
                "observation" => $request->input("observation")
            ];

            $buscaActualiza->update($data);
            return response()->json(["message" => "Registro actualizado con éxito"], 200);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}
