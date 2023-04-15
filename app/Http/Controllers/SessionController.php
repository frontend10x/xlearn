<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Salida\LogoutController;

class SessionController extends Controller
{
    /**
     * Reportar actividad del usuario en la plataforma (activo/inactivo)
     */
    public static function changeStateUser(Request $request)
    {
        try {

            // Validamos los datos enviados
            $validated = $request->validate([
                'id' => 'required|integer|exists:users',
                'state' => 'required|boolean',
            ]);

            $id = $request->input("id");
            $state = $request->input("state");

            $user = User::find($id);
            $user->update([ "currently_active" => $state ]);
            $logout = ['session' => true, 'message' => 'Sesión activa'];

            if(!$state)
                $logout = LogoutController::logout($request);


            return $logout;


        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}