<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Coupon;

class PaymentController extends Controller
{

    /**
    * @OA\Post(
    *     path="/api/v1/payment/requests",
    *     tags={"Payments"},
    *     summary="Obtener detalles de pago",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="name", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="email", required=true, in="query", @OA\Schema(type="string")),
    *     @OA\Parameter(name="amount_user", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="amount_time", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="subcompanie_id", required=true, in="query", @OA\Schema(type="number")),
    *     @OA\Parameter(name="coupon", in="query", @OA\Schema(type="string")),
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
    *                           "coupon_status": "",
    *                           "plan": "{}",
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
                'name' => 'required',
                'email' => 'required',
                'subcompanie_id' => 'required|integer',
                'amount_user' => 'required|integer',
                'amount_time' => 'required|integer'
            ]);

            $coupon_status = false;

            if (!empty($request->input("coupon"))) {

                $coupon = Coupon::where("code", $request->input("coupon"))
                                ->where("validity", '>=', date('Y-m-d H:m:s'))
                                ->first();

                if (!empty($coupon)){
                    $coupon_status = true; 
                    $percentaje = $coupon->percentage;
                }
            }

            $amount_user = $request->input("amount_user");
            $amount_time = $request->input("amount_time");

            //Generamos la referencia de pago
            $reference = $request->input("subcompanie_id") . '-' . strtotime(date('Y-m-d H:m:s'));

            $PUBLIC_KEY = env('PUBLIC_TEST_KEY_WOMPY');
            $CURRENCY = env('CURRENCY');

            //Consultamos el plan acorde a la cantidad de usuario y tiempo
            $plan = PaymentController::calculate_value_to_pay($amount_user, $amount_time);

            //Calculamos el valor a pagar
            $amount_to_paid = $plan->price * $amount_time * $amount_user;

            //Aplicacion de descuento si es efectivo el cupon
            if($coupon_status)
                $amount_to_paid = $amount_to_paid - $amount_to_paid * $percentaje / 100;

            //Generamos la firma de integridad para el pago (WOMPY)
            $connected_string = $reference . $amount_to_paid . $CURRENCY . $PUBLIC_KEY;
            $integrity_signature = hash("sha256", $connected_string);

            $dataInsert = [
                "reference" => $reference, 
                "name" => $request->input("name"), 
                "email" => $request->input("email"), 
                "amount" => $amount_to_paid, 
                "plan_id" => $plan->id, 
                "amount_time" => $amount_time, 
                "amount_user" => $amount_user, 
                "subcompanie_id" => $request->input("subcompanie_id"), 
                "status" => 'pending',
                "coupon_id" => (!empty($coupon)) ? $coupon->id : NULL,
                "signature" => $integrity_signature,
                "currency" => $CURRENCY
            ];

            $created = Payment::create($dataInsert);

            $payment_details = [
                "amount" => $amount_to_paid,
                "reference" => $reference,
                "currency" => $CURRENCY,
                "public_key" => $PUBLIC_KEY,
                "signature" => $integrity_signature,
                "coupon_status" => ($coupon_status) ? 'Cupon aplicado con descuento del ' . $percentaje . '%' : 'Cupon no relacionado o no valido',
                "plan" => $plan
            ];

            return response()->json(["payment_details" => $payment_details], 200);

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine()], 500);
        }
    }

    public static function calculate_value_to_pay($amount_user, $amount_time)
    {
        try {

            $plan = Plan::select('id', 'price', 'name', 'description', 'amount_user', 'amount_time')->where('amount_user', '<=', $amount_user)->where('amount_time', '<=', $amount_time)->where('state', 1)->orderBy('amount_user', 'desc')->orderBy('amount_time', 'desc')->limit(1)
            ->first();

            return $plan;

        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "line" => $e->getLine()], 500);
        }
    }
}
