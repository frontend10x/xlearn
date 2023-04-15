<?php

namespace App\Http\Controllers\Salida;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public static function logout(Request $request)
    {
        try {
            
            $request->user()->token()->revoke();

            return response()->json([
                'session' => false,
                'message' => 'Sesión cerrada'
            ]);

        } catch (Exception $e) {

            return returnExceptions($e);

        }
    }
}