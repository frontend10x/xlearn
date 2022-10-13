<?php

namespace App\Http\Controllers\companies\group;

use App\Http\Controllers\Controller;
use App\Models\companies\group\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{

    /**
    * @OA\Post(
    *     path="/api/v1/group/store",
    *     tags={"Groups"},
    *     summary="Crear grupo",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="description", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="file_path", in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="subcompanies_id", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Registro almacenado con éxito."
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
            $subCompany = Group::where("name", $request->input("name"))->first();
            if (!empty($subCompany)) {
                throw new Exception("Ya existe el grupo con el nombre " . $request->input("name"));
            }

            // Validamos los datos enviados
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string'
            ]);

            $datosSubEmpresa = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }

            if (!empty(Auth::user()->subcompanies_id)) {
                $datosSubEmpresa['subcompanies_id'] = Auth::user()->subcompanies_id;
            } else if (!empty($request->input("subcompanies_id"))) {
                $datosSubEmpresa['subcompanies_id'] = $request->input("subcompanies_id");
            }

            Group::create($datosSubEmpresa);
            return response()->json(["message" => "Grupo creado con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $subEmpresa = Group::find($id);
            $datosSubEmpresa = [
                "name" => $request->input("name"), "description" => $request->input("description"), "state" => $request->input("state")
            ];
            if (!empty($request->input("file_path"))) {
                $datosSubEmpresa['file_path'] = $request->input("file_path");
            }
            if (empty($subEmpresa)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            } else {
                $subEmpresa->update($datosSubEmpresa);
                $message = "Grupo actualizado con éxto";
            }

            if (!empty(Auth::user()->subcompanies_id)) {
                $datosSubEmpresa['subcompanies_id'] = Auth::user()->subcompanies_id;
            } else if (!empty($request->input("subcompanies_id"))) {
                $datosSubEmpresa['subcompanies_id'] = $request->input("subcompanies_id");
            }

            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/group/list",
    *     summary="Mostrar grupos de usuarios",
    *     tags={"Groups"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los grupos de usuarios.",
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
    *                               "groups": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "description": "",
    *                                   "subcompanies_id": ""
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
                $grupos = Group::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            } else {

                //TODO debe sacarse del request, por defecto el valor es uno
                $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

                //TODO debe sacarse del request, por defecto el valor es 10.
                $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

                $consult = Group::select('id', 'name', 'description', 'subcompanies_id')->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

                $nexOffset = $offset + 1;
                $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            }

            $groups = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => Group::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "groups",
                "_embedded" => array(
                    "groups" => $consult
                )
            );
            return response()->json(["groups" => $groups], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Group::find($id);
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el Id:" . $id . " para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/groupuser/assignment/{group_id}",
    *     tags={"User groups"},
    *     summary="Asignación de usuarios a grupo",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="user", required=true, in="query", @OA\Schema(type="[]")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Usuarios asignados a grupo con éxito."
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
    public function assignment(Request $request, $group_id)
    {
        try {
            
            $group = Group::find($group_id);
            $group->users()->sync($request->user);
            
            foreach ($request->user as $key) {
                $user = User::find($key);
                $user->update(['group_id' => $group_id]);
            }
            return json_encode(["message" => "Usuarios asignados a grupo con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Deleted(
    *     path="/api/v1/groupuser/removefromgroup/{group_id}",
    *     tags={"User groups"},
    *     summary="Remover usuario de grupo",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="user", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "message":"Usuario removido de grupo con éxito."
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
    public function removefromgroup(Request $request, $group_id)
    {
        try {
            $group = Group::find($group_id);
            $group->users()->detach($request->user);
            return response()->json(["users" => $group->users], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    //Listar los usuarios que pertenecen al grupo
    public function listGroupUsers($group_id)
    {
        try {
            $group = Group::find($group_id);
            return response()->json(["users" => $group->users], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    //listar los grupos al que pertenece el usuario
    public static function listUserGroups($user_id)
    {
        try {

            $group = DB::table('user_group')
                        ->join('groups', 'groups.id', 'user_group.group_id')
                        ->select('group_id', 'groups.name', 'groups.description')
                        ->where('user_group.user_id', $user_id)
                        ->get();

            return response()->json([$group], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/v1/group/list_company_group/{subcompanie_id}",
    *     summary="Mostrar los grupos de la empresa",
    *     tags={"Groups"},
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="subcompanie_id", in="path", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los grupos de la empresa.",
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
    *                               "groups": {
    *                                   {
    *                                   "id": 0,
    *                                   "name": "",
    *                                   "description": "",
    *                                   "subcompanies_id": ""
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

    //listar los grupos de la empresa
    public static function listCompanyGroups(Request $request, $subcompanie_id)
    {

        try {
            if (!empty(Auth::user()->subcompanies_id)) {
                $consult = Group::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            } 

                //TODO debe sacarse del request, por defecto el valor es uno
                $offset = $request->has('offset') ? intval($request->get('offset')) : 1;

                //TODO debe sacarse del request, por defecto el valor es 10.
                $limit = $request->has('limit') ? intval($request->get('limit')) : 10;

                if( !empty($request->has('offset')) &&  !empty($request->has('limit'))){

                    $consult = Group::select('id', 'name', 'description', 'subcompanies_id')->where('subcompanies_id', $subcompanie_id)->limit($limit)->offset(($offset - 1) * $limit)->get()->toArray();

                }else{

                    $consult = Group::select('id', 'name', 'description', 'subcompanies_id')->where('subcompanies_id', $subcompanie_id)->get()->toArray();

                }
                
                $nexOffset = $offset + 1;
                $previousOffset = ($offset > 1) ? $offset - 1 : 1;

            

            $groups = array(
                "hc:length" => count($consult), //Es la longitud del array a devolver
                "hc:total"  => Group::count(), //Es la longitud total de los registros disponibles en el query original,
                "hc:offset" => $offset,
                "hc:limit"  => $limit,
                "hc:next"   => server_path() . '?limit=' . $limit . '&offset=' . $nexOffset,
                "hc:previous"   => server_path() . '?limit=' . $limit . '&offset=' . $previousOffset,
                "_rel"		=> "groups",
                "_embedded" => array(
                    "groups" => $consult
                )
            );
            return response()->json(["groups" => $groups], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function delete($group_id){
        try {
            $group = Group::find($group_id);
            $group->delete();
            return response()->json(["message" => "Grupo eliminado con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
