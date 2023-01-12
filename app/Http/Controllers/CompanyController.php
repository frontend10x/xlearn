<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\Sub_companies;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{

    public function index()
    {
        try {
            $company = Auth::user()->subcompanies_id;
            if(empty($company)){
                $dataCompany = Companies::first();
            }else{
                $dataCompany = Sub_companies::find($company); 
            }
            return response()->json(["company" => $dataCompany], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $empresa = Companies::first();
            $datosEmpresa = [
                 "link_facebook" => $request->input("link_facebook")
                , "link_google" => $request->input("link_google")
                , "link_linkedin" => $request->input("link_linkedin")
                , "link_instagram" => $request->input("link_instagram")
                , "website" => $request->input("website")
                , "nit" => $request->input("nit")
                ,"name" => $request->input("name"), 
                "address" => $request->input("address"), 
                "phone" => $request->input("phone"), 
                "representative" => $request->input("representative"), 
                "position" => $request->input("position"), 
                "representative_cell" => $request->input("representative_cell"), "file_path" => $request->input("file_path")
            ];
            if (empty($empresa)) {
                Companies::create($datosEmpresa);
                $message = "Empresa creada con éxto";
            } else {
                $empresa->update($datosEmpresa);
                $message = "Empresa actualizada con éxto";
            }
            return response()->json(["message" => $message], 200);
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    
}
