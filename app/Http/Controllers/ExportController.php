<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SampleFile;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

    /**
    * @OA\get(
    *     path="/api/v1/export/sample_file",
    *     tags={"Exports"},
    *     summary="Exportar documento de muestra",
    *     security={{"bearer_token":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="{Download Sample File}",
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
    public function exportSampleFile() 
    {
        return Excel::download(new SampleFile, 'users.xlsx');
    }
}
