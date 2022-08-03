<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    /**
    * @OA\Post(
    *     path="/api/v1/payment/requests",
    *     tags={"Payments"},
    *     summary="Obtener detalles de pago",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="reference", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="amount", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                       "payment_details": {
    *                           "amount": "",
    *                           "reference": "",
    *                           "currency": "",
    *                           "public_key": "",
    *                           "signature": "",
    *                        }
    *                 },
    *             ),
    * 
    *         ),
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
    public function paymentRequests(Request $request)
    {
        try {

            // Validamos los datos enviados
            $validated = $request->validate([
                'reference' => 'required',
                'amount' => 'required|integer'
            ]);

            $PUBLIC_KEY = env('PUBLIC_TEST_KEY_WOMPY');
            $CURRENCY = env('CURRENCY');

            $connected_string = $request->input("reference") . $request->input("amount") . $CURRENCY . $PUBLIC_KEY;
            
            $integrity_signature = hash("sha256", $connected_string);

            $payment_details = [
                "amount" => $request->input("amount"),
                "reference" => $request->input("reference"),
                "currency" => $CURRENCY,
                "public_key" => $PUBLIC_KEY,
                "signature" => $integrity_signature
            ];

            return response()->json(["payment_details" => $payment_details], 200);

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
