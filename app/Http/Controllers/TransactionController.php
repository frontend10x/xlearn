<?php

namespace App\Http\Controllers;

use Mail;
use Exception;
use DateTime;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Payment;
use App\Mail\EmailNotification;


class TransactionController extends Controller
{
    public static function store(Request $request)
    {   
        
        try {
            
            if (empty($request->input("data")))
                throw new Exception("Data not found");

            $data = $request->input("data");
            $signatue = $request->input("signature");
            $timestamp = $request->input("timestamp");
            $event_type = $request->input("event");
            $environment = $request->input("environment");
            $sent_at = $request->input("sent_at");

            $reference = $data['transaction']['reference'];

            $search_payment_request = Payment::where('reference', $reference)->first();

            if (empty($search_payment_request))
                throw new Exception("No existe la referencia");

            $validate_signature = validate_signature($data, $signatue, '.', $timestamp);

            if (!$validate_signature)
                throw new Exception("Firma no autorizada");

                $dt = new DateTime($sent_at);

                $dataInsert = [
                    "payment_gateway" => 'wompi', 
                    "payment_gateway_transaction_id" => $data['transaction']['id'], 
                    "status" => $data['transaction']['status'],
                    "reference" => $data['transaction']['reference'], 
                    "amount" => $data['transaction']['amount_in_cents'], 
                    "monetary_fraction" => 'cents', 
                    "currency" => $data['transaction']['currency'], 
                    "customer_email" => $data['transaction']['customer_email'], 
                    "payment_method_type" => $data['transaction']['payment_method_type'],  
                    "environment" => $environment, 
                    "event_type" => $event_type, 
                    "sent_at_paymnet" => $dt->format('Y-m-d') . ' ' . $dt->format('H:i:s'), 
                    "timestamp_paymnet" => $timestamp,
                    "response_payment_gateway" => json_encode($request->input())
                ];
    
                $created = Transaction::create($dataInsert);

                if ($dataInsert['status'] == 'APPROVED') {
                    
                    Mail::to($data['transaction']['customer_email'])->send(new EmailNotification($dataInsert, 'payment_register'));

                }


                $update_payment = PaymentController::update_payment_status(
                    $search_payment_request['id'],
                    $data['transaction']['status'], 
                    $created['id']
                );
            
            return response()->json(["created" => $created, "update" => $update_payment], 200);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
        
    }
}
