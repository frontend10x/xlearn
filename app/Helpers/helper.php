<?php

if (! function_exists('server_path')) {
    function server_path()
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

        $path = ( env('AMBIENT') === 'DEV' ) ? $_SERVER['PATH_INFO'] : $_SERVER['REDIRECT_SCRIPT_URL'];
        
        return $protocol . $_SERVER['HTTP_HOST'] . $path;
    }
}

if (! function_exists('get_ids')) {
    function get_ids($array, $keyword)
    {

        $ids = [];

        foreach ($array as $key => $value) {
            array_push($ids, $value[$keyword]);
        }
        
        return $ids;
    }
}

if (! function_exists('return_exceptions')) {
    function return_exceptions($e)
    { 
        $logFile = fopen("log_failed.txt", 'a') or die("Error creando archivo");

        fwrite(
            $logFile, 
            "\n\n" . date("d/m/Y H:i:s") . 
            "\n"   . "message => " . $e->getMessage() .
            "\n"   . "file => " . $e->getFile() .
            "\n"   . "line => " . $e->getLine() 
        ) or die("Error escribiendo en el archivo");
        
        fclose($logFile);

        return response()->json([
            "message" => $e->getMessage(), 
            "line" => $e->getLine(), 
            "file" => $e->getFile()
        ], 500);
    }
}

if(!function_exists('validate_signature')) {
    function validate_signature($data, $signatue, $div, $timestamp)
    {   
        $properties = '';
        $isValid = false;

        foreach($signatue['properties'] as $value){
            
            $prop = explode($div, $value);
            
            $properties .= $data[$prop[0]][$prop[1]];
        
        }

        $secret = ( env('AMBIENT') === 'DEV' ) ? env('SECRET_TEST_EVENTS_WOMPY') : env('SECRET_PROD_EVENTS_WOMPY');

        $calculated_signature = hash( "sha256", $properties . $timestamp . $secret );

        if($calculated_signature === $signatue['checksum']) $isValid = true;

        return $isValid;
    }
}

if(!function_exists('save_file')) {
    function save_file($data)
    {
        $logFile = fopen("log_data.txt", 'a') or die("Error creando archivo");

        fwrite(
            $logFile, 
            "\n\n" . date("d/m/Y H:i:s") . 
            "\n"   . "data => " . $data 
        ) or die("Error escribiendo en el archivo");
        
        fclose($logFile);
    }
}

if (!function_exists('calculate_amount_in_cents')) {
    function calculate_amount_in_cents($amount_to_paid, $coupon_status, $percentage){

        //(convertimos el valor a pagar en centavos (x 100))
        $amount_centies = $amount_to_paid * 1000;

        //Aplicacion de descuento si es efectivo el cupon
        if($coupon_status)
            $amount_centies = $amount_centies - $amount_centies * $percentage / 100;
        
        return $amount_centies;
    }
}