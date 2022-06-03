<?php

namespace App\Http\Controllers;

use App\Models\Contact_us;
use Exception;
use Illuminate\Http\Request;

class Contact_usController extends Controller
{
    public function store(Request $request)
    {
        try {
            Contact_us::create([
                "name" => $request->input("name"), "phone" => $request->input("phone"), "position" => $request->input("position"), "observation" => $request->input("observation")
            ]);
            return response()->json(["message" => "Datos almacenados con éxito"], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            return response()->json(["contact_us" => Contact_us::all()], 200);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
