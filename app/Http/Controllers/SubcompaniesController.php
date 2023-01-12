<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\Sub_companies;
use Exception;
use Illuminate\Http\Request;

class SubcompaniesController extends Controller
{

    public function dataCompanyXId($id)
    {
        try {

            $subcompany = Sub_companies::find($id);
            if(empty($subcompany))
                throw new Exception("No existe la compañia con el id: ".$id);

            return response()->json(["sub_company" => $subcompany], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public static function store(Request $request){
        try {
            $subCompany = Sub_companies::where("name",$request->input("company"))->first();
            if(!empty($subCompany)){
                throw new Exception("La compañia ya se encuentra registrada");
            }
            $subCompanyNit = Sub_companies::where("nit",$request->input("nit"))->first();
            if(!empty($subCompanyNit)){
                throw new Exception("El nit de la compañia ya se encuentra registrado");
            }
            $datosSubEmpresa = [
                "name" => $request->input("company"), 
                "address" => $request->input("address"), 
                "phone" => $request->input("phone")
                , "representative" => $request->input("representative")
                , "position" => $request->input("position")
                , "representative_cell" => $request->input("representative_cell")
                , "start_date" => $request->input("start_date")
                , "end_date" => $request->input("end_date")
                , "unlimited_access" => $request->input("unlimited_access") ?? 0
                , "file_path" => $request->input("file_path")
                , "link_facebook" => $request->input("link_facebook")
                , "link_google" => $request->input("link_google")
                , "link_linkedin" => $request->input("link_linkedin")
                , "link_instagram" => $request->input("link_instagram")
                , "website" => $request->input("website")
                , "nit" => $request->input("nit")
            ];
            $created = Sub_companies::create($datosSubEmpresa);
            return json_encode(["message" => "Registro almacenado con éxito ", "id" => $created['id']]);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function edit(Request $request,$id){
        try {
            $subEmpresa = Sub_companies::find($id);
            $datosSubEmpresa = [
                "name" => $request->input("name")
                , "address" => $request->input("address")
                , "phone" => $request->input("phone")
                , "representative" => $request->input("representative")
                , "position" => $request->input("position")
                , "representative_cell" => $request->input("representative_cell")
                , "start_date" => $request->input("start_date")
                , "end_date" => $request->input("end_date")
                , "unlimited_access" => $request->input("unlimited_access") ?? 0
                , "link_facebook" => $request->input("link_facebook")
                , "link_google" => $request->input("link_google")
                , "link_linkedin" => $request->input("link_linkedin")
                , "link_instagram" => $request->input("link_instagram")
                , "website" => $request->input("website")
                , "nit" => $request->input("nit")
                , "file_path" => $request->input("file_path")
            ];
            if (empty($subEmpresa)) {
                throw new Exception("No existe el id: ".$id." para ser actualizado");
            } else {
                $subEmpresa->update($datosSubEmpresa);
                $message = "Empresa actualizada con éxto";
            }
            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function index(){
        try {
            return response()->json(["sub_companies" => Sub_companies::all()], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
    public function changestate(Request $request, $id)
    {
        try {
            $buscaActualiza = Sub_companies::find($id);
            if(empty($buscaActualiza)){
                throw new Exception("No existe el Id:".$id." para el cambio de estado");
            }
            $buscaActualiza->update(["state" => $request->input("state")]);
            return response()->json(["message" => "Cambio de estado correctamente"], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }
}
