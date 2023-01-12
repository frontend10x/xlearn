<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public static function store(Request $request, $validate = true)
    {
        try {

            if($validate){
                $program = Program::where("name", $request->input("name"))->first();
                if (!empty($program)) {
                    throw new Exception("Ya existe un programa con el nombre " . $request->input("name"));
                }
            }
            
            $dataInsert = [
                "name" => $request->input("name"), 
                "description" => $request->input("description"),
                "area_id" => $request->input("area_id"),
                "vimeo:uri" => $request->input("vimeo:uri"),
                "vimeo:id" => $request->input("vimeo:id")
            ];

            $create_program = Program::create($dataInsert);
            return json_encode(["message" => "Programa creado con éxito", "id" => $create_program['id']]);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public static function sync_with_vimeo(Request $request)
    {
        try {

            $program = Program::where("vimeo:id", $request->input("vimeo:id"))->first();

            if (empty($program)) {
                $state = self::store($request, false);
                return json_encode($state);
            }else{
                $state = self::edit($request);
                return json_encode($state->original);
            }

            save_file($state);

        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public static function edit(Request $request)
    {
        try {

            $datosInsertar = Program::where("vimeo:id", $request->input("vimeo:id"))->first();

            $id = $request->input("vimeo:id");
            
            $data = [
                "name" => $request->input("name"), 
                "description" => $request->input("description"),
                "vimeo:uri" => $request->input("vimeo:uri"),
                "vimeo:id" => $request->input("vimeo:id")
            ];

            if (!empty($request->input("area_id"))) {
                $data['area_id'] = $request->input("area_id");
            }

            if (empty($datosInsertar)) {
                throw new Exception("No existe el id: " . $id . " para ser actualizado");
            }

            $update_course = $datosInsertar->update($data);            

            return response()->json(["message" => "Programa actualizado con éxto", "id" => $datosInsertar->id], 200);
        
        } catch (Exception $e) {

            return return_exceptions($e);

        }
    }
}
