<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $consult = User::where("email", $request->input("email"))->first();
            if (!empty($consult)) {
                throw new Exception("El usuario ya se encuentra registrado");
            }
            $dataInsert = [
                "link_facebook" => $request->input("link_facebook"), "link_google" => $request->input("link_google"), "link_linkedin" => $request->input("link_linkedin"), "link_instagram" => $request->input("link_instagram"), "name" => $request->input("name"), "surname" => $request->input("surname"), "phone" => $request->input("phone"), "email" => $request->input("email"), "state" => $request->input("state"), "password" => Hash::make($request->input("password"))
            ];
            if (!empty($request->input("subcompanies_id"))) {
                $dataInsert['subcompanies_id'] = $request->input("subcompanies_id");
            }
            User::create($dataInsert);
            return response()->json(["message" => "Registro almacenado con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $buscaActualiza = User::find($id);
            $dataUpdate = [
                "link_facebook" => $request->input("link_facebook"), "link_google" => $request->input("link_google"), "link_linkedin" => $request->input("link_linkedin"), "link_instagram" => $request->input("link_instagram"), "name" => $request->input("name"), "surname" => $request->input("surname"), "phone" => $request->input("phone"), "email" => $request->input("email"), "state" => $request->input("state")
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
                $message = "usuario actualizado con éxto";
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
                $user = User::where("subcompanies_id", Auth::user()->subcompanies_id)->get();
            } else {
                $user = User::all();
            }
            return response()->json(["user" => $user], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = User::find($id);
            if (empty($buscaActualiza)) {
                throw new Exception("No existe el Id:" . $id . " para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function forgotpassword(Request $request, $id)
    {
        try {
            $id = $id;
            $user = User::find($id);

            if (empty($user))
                throw new Exception("No existe usuario para modificar contraseña");

            $user->password = Hash::make($request->input("password"));
            $user->save();
            return response()->json(["message" => "Contraseña actualizada con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function changepassword(Request $request)
    {
        try {

            $user = User::find(Auth::user()->id);
            $user->password =  Hash::make($request->input("password"));
            $user->save();

            return response()->json(["message" => "password modificado con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
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
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
