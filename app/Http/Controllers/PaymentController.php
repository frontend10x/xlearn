<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Coupon;

define('CURRENCY', validate_environment()['CURRENCY']);
define('SECRET_INTEGRITY', validate_environment()['SECRET:INTEGRITY']);
define('PUBLIC_KEY', validate_environment()['PUBLIC_KEY']);
define('COMPANY_ROLE_NAME', 'Empresa');

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

            $VAR_ENV = validate_environment();

            $coupon_status = false;
            $percentage = 1;

            if (!empty($request->input("coupon"))) {

                $validate = self::validate_coupon($request->input("coupon"));

                $coupon_status = $validate['status']; 
                $percentage = ($validate['status']) ? $validate['percentage'] : $percentage;

            }

            $amount_user = $request->input("amount_user");
            $amount_time = $request->input("amount_time");

            //Generamos la referencia de pago
            $reference = $request->input("subcompanie_id") . '-' . strtotime(date('Y-m-d H:m:s'));

            //Consultamos el plan acorde a la cantidad de usuario y tiempo
            $plan = PaymentController::consult_value_to_pay($amount_user, $amount_time);

            //Calculamos el valor a pagar en pesos
            $amount_to_paid = ($plan->price * 5000) * $amount_time * $amount_user;
            $amount_centies = calculate_amount_in_cents($amount_to_paid, $coupon_status, $percentage);
            
            //Generamos la firma de integridad para el pago (WOMPY)
            $integrity_signature = calculate_signature([$reference, $amount_centies, CURRENCY, SECRET_INTEGRITY]);

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
                "currency" => CURRENCY
            ];

            $created = Payment::create($dataInsert);

            $payment_details = [
                "amount_cents" => $amount_centies,
                "amount_pesos" => $amount_centies / 100,
                "reference" => $reference,
                "currency" => CURRENCY,
                "public_key" => PUBLIC_KEY,
                "signature" => $integrity_signature,
                "coupon_status" => ($coupon_status) ? 'Cupon aplicado con descuento del ' . $percentage . '%' : 'Cupon no relacionado o no valido',
                "plan" => $plan
            ];

            return response()->json(["payment_details" => $payment_details], 200);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public static function consult_value_to_pay($amount_user, $amount_time)
    {
        try {

            $plan = Plan::select('id', 'price', 'name', 'description', 'amount_user', 'amount_time')->where('amount_user', '<=', $amount_user)->where('amount_time', '<=', $amount_time)->where('state', 1)->orderBy('amount_user', 'desc')->orderBy('amount_time', 'desc')->limit(1)
            ->first();

            return $plan;

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    /**
    * @OA\Post(
    *     path="/api/v1/payment/approved_payment_status",
    *     tags={"Payments"},
    *     summary="Verificar cupos de empresa",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(name="subcompanie_id", in="query", @OA\Schema(type="number")),
    *     @OA\Response(
    *         response=200,
    *         description="Success.",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                  example={
    *                      "quotas":0,
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
    public static function approvedPaymentStatus(Request $request)
    {
        try {
            
            // Validamos los datos enviados
            $validated = $request->validate([
                'subcompanie_id' => 'required|integer|exists:payments',
                'subcompanie_id' => 'exists:users,subcompanies_id'
            ]);

            $payments = Payment::where('subcompanie_id', $request->subcompanie_id)->where('status', 'APPROVED')->get()->toArray();

            if(!$payments)
                throw new Exception('La empresa no tiene cupos disponibles');

            $quotas = 0;
            $amountUsers = 0;

            foreach($payments AS $payment){
                $quotas += $payment['amount_user'];
            }

            $users = UserController::showUserSubCompanie($request, $request->subcompanie_id);
            $key = $users->original['response']['_rel'];
            $arrayUsers = $users->original['response']['_embedded'][$key];
            
            foreach($arrayUsers AS $value){

                /* Se comenta estas lineas de código con el objetivo de permitir al usuario
                * empresa sea asignado a un 
                *
                */
                $rol_name = RolesController::showNameById($value['rol_id']);

                if($rol_name != COMPANY_ROLE_NAME)
                    $amountUsers++;
            }

            $calculateQuotas = $quotas - $amountUsers;

            $payments['quotas'] = $calculateQuotas;

            if($calculateQuotas <= 0 )
                $payments['quotas'] = 0;

            return response()->json(["quotas" => $payments['quotas']], 200);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public static function validate_coupon($coupon)
    {
        try {
            
            $coupon = Coupon::where("code", $coupon)
                                ->where("validity", '>=', date('Y-m-d H:m:s'))
                                ->first();

            if (!empty($coupon)){

                return [
                    "status" => true,
                    "percentage" => $coupon->percentage
                ];

            }

            return [ "status" => false ];

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public static function update_payment_status($id, $status, $transaction_id)
    {
        try {
            
            $buscaActualiza = Payment::find($id);

            if (empty($buscaActualiza)) {
                throw new Exception("Ocurrio un error");
            }

            $dataUpdate = [
                'status' => $status,
                'transaction_id' => $transaction_id
            ];

            $update = $buscaActualiza->update($dataUpdate);

            return $update;


        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    
    }
}