<?php

namespace App\Http\Controllers\companies\group;

use App\Http\Controllers\Controller;
use App\Models\companies\group\Group;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        try {
            $subCompany = Group::where("name", $request->input("name"))->first();
            if (!empty($subCompany)) {
                throw new Exception("Ya existe el grupo con el nombre " . $request->input("name"));
            }
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
    public function index(Request $request)
    {
        try {
            if (!empty(Auth::user()->subcompanies_id)) {
                $grupos = Group::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            } else {
                $grupos = Group::all();
            }
            return response()->json(["groups" => $grupos], 200);
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

    public function assignment(Request $request, $group_id)
    {
        try {
            $group = Group::find($group_id);
            $group->users()->sync($request->user);
            return response()->json(["users" => $group->users], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
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
    public function listUserGroup($group_id)
    {
        try {
            $group = Group::find($group_id);
            return response()->json(["users" => $group->users], 200);
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
