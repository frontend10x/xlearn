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

if (! function_exists('get_id_vimeo')) {
    function get_id_vimeo($uri)
    {

        $id = explode("/", $uri);
        
        return $id[4];
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

        $varEnv = validate_environment();

        $secret = $varEnv['SECRET:EVENTS'];
        
        $calculated_signature = calculate_signature([$properties, $timestamp, $secret]);

        if($calculated_signature === $signatue['checksum']) $isValid = true;

        return $isValid;
    }
}

if(!function_exists('calculate_signature')) {
    function calculate_signature($arrayStr){
        
        $concatenatedString = '';

        foreach ($arrayStr as $str) {

            $concatenatedString .= $str;

        }

        return hash( "sha256", $concatenatedString );
    }
}

if(!function_exists('save_file')) {
    function save_file($data)
    {
        $logFile = fopen("log_data.txt", 'a') or die("Error creando archivo");

        fwrite(
            $logFile, 
            "\n\n" . date("d/m/Y H:i:s") . 
            "\n"   . "data => " . json_encode($data) 
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

if(!function_exists('validate_environment')){
    function validate_environment(){

        $CURRENCY = env('CURRENCY');

        $SECRET_INTEGRITY = env('SECRET_PROD_INTEGRITY_WOMPY');
        $SECRET_EVENTS = env('SECRET_PROD_EVENTS_WOMPY');
        $PUBLIC_KEY = env('PUBLIC_PROD_KEY_WOMPY');
        $API_VERSION = env('API_VERSION');
        $URL_BASE = env('URL_BASE_PRODUCTION');
        $URL_FRONT = env('URL_FRONT');

        if (env('AMBIENT') === 'DEV') {
            $SECRET_INTEGRITY = env('SECRET_TEST_INTEGRITY_WOMPY');
            $SECRET_EVENTS = env('SECRET_TEST_EVENTS_WOMPY');
            $PUBLIC_KEY = env('PUBLIC_TEST_KEY_WOMPY');
            $URL_BASE = env('URL_BASE_LOCAL');
        }

        $variables = [
            'CURRENCY'          => $CURRENCY,
            'SECRET:INTEGRITY'  => $SECRET_INTEGRITY,
            'SECRET:EVENTS'     => $SECRET_EVENTS,
            'PUBLIC_KEY'        => $PUBLIC_KEY,
            'API_VERSION'       => $API_VERSION,
            'URL_BASE'          => $URL_BASE,
            'URL_FRONT'         => $URL_FRONT
        ];

        return $variables;
    }
}