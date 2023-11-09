<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson_user_note;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function addNote(Request $request, $id)
    {
        try {

            $validate = $request->validate([
                'note' => 'required'
            ]);

            $insertado = Lesson_user_note::create([
                "lessonId" => $id, 
                "userId" => Auth::user()->id, 
                "note" => $request->input("note"), 
                "timeSecond" => $request->input("timeSecond")
            ]);
                
            if ($insertado)
                return response()->json([
                                "status" => "success",
                                "message" => "Nota insertada correctamente",
                                "data" => $insertado
                            ], 200);
            else
                throw new Exception("Error en la inserción de la nota");
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function listNote($id)
    {
        try {

            $notes = Lesson_user_note::with(['user'])
                                            ->where('lessonId', $id)
                                            ->where('userId', Auth::user()->id)
                                            ->get();

            return response()->json(["notes" => $notes], 200);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}