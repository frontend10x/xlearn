<?php


/**
 * Consultar url server del proyecto
 */
if (! function_exists('server_path')) {
    function server_path()
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

        $path = ( env('AMBIENT') === 'DEV' ) ? $_SERVER['PATH_INFO'] : $_SERVER['REDIRECT_SCRIPT_URL'];
        
        return $protocol . $_SERVER['HTTP_HOST'] . $path;
    }
}

/**
 * Obtener un arra de "id"
 */
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
        $amount_centies = $amount_to_paid * 100;

        //Aplicacion de descuento si es efectivo el cupon
        if($coupon_status)
            $amount_centies = $amount_centies - $amount_centies * $percentage / 100;
        
        return $amount_centies;
    }
}

if (!function_exists('progress')) {
    function progress($progress, $total_video_time = false){

        $percentage = 0;
        $percentage_completion = 0;
        $advanced_current_time = 0;
        //$total_video_time = 0;

        if (isset($progress->original['progress'])){

            $result = $progress->original['progress'];

            foreach ($result as $pro) {
                $percentage_completion += $pro['percentage_completion'];
                $advanced_current_time += $pro['advanced_current_time'];
                //$total_video_time += $pro['total_video_time'];
            }

            $total_video_time = $total_video_time ? $total_video_time : 1;

            $percentage = round($advanced_current_time / $total_video_time * 100);

        }
        
        return $percentage;
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

/**
 * Mapeador de data, se utiliza para mapear la información obtenida 
 * desde otro controlador y bajo el estandar establecido:
 * _rel; nombre de la llave que trae la data en el _embedded
 */
if (!function_exists('data_mapper')) {
    function data_mapper($data, $keyword = "response"){

        if(isset($data->original[$keyword])){
            $response = $data->original[$keyword];

            if(!isset($response["_rel"])){
                return $response;
            }
            
            $key = $response["_rel"];
            $responseData = $response["_embedded"][$key];
            return $responseData;
        }

        return [];
        
    }
}

/**
 * Conteo de elementos en un array de objetos
 * si cumplen la condición dada
 */
if (!function_exists('count_keys')) {
    function count_keys($arrayData, $keyword, $valueToCompare){

        $amount = 0;

        foreach ($arrayData as $key => $value) {
            if($value[$keyword] == $valueToCompare){
                $amount++;
            }
        }
    
        return $amount;
    }
}

/**
 * Suma de valor de elementos en un array de objetos
 */
if (!function_exists('sum_keys')) {
    function sum_keys($arrayData, $keyword){

        $amountValue = 0;

        foreach ($arrayData as $key => $value) {
            $amountValue =  $amountValue + $value[$keyword];
            
        }
    
        return $amountValue;
    }
}

/**
 * Convertir segundos a hours, minutos y segundos
 */
if (!function_exists('handle_seconds')) {
    function handle_seconds($timeSeconds) {
        $hours = floor($timeSeconds / 3600);
        $minuts = floor(($timeSeconds - ($hours * 3600)) / 60);
        $seconds = $timeSeconds - ($hours * 3600) - ($minuts * 60);

        $result = "";
        if ($hours > 0 ) {
            $result .= $hours . "h ";
        }

        if ($minuts > 0 ) {
            $result .= $minuts . "m ";
        }

        if ($seconds > 0 ) {
            $result .= $seconds . "s";
        }

        return $result;
    }
}

/**
 * Validamos si una hora dada, se encuentra
 * dentro de la jornada lavorar
 */
if (!function_exists('is_working_time')) {
    function is_working_time($hour) {
        if($hour >= env('WORK_START_TIME') && $hour <= env('WORK_END_TIME')) return true;
        return false;
    }
}

/**
 * Validamos si una hora dada, se encuentra
 * dentro de la jornada lavorar
 */
if (!function_exists('is_working_time')) {
    function is_working_time($hour) {
        if($hour >= env('WORK_START_TIME') && $hour <= env('WORK_END_TIME')) return true;
        return false;
    }
}

/**
 * sumar meses a una fecha dada
 */
if (!function_exists('add_months')) {
    function add_months($date, $months) {

        $d = date('d', strtotime($date));
        $m = date('m', strtotime($date));
        $y = date('Y', strtotime($date));

        if(!checkdate($m, $d, $y)){
            throw new Exception("Incorrect date");
        };

        $formatDate = date_create($date);
        date_add($formatDate, date_interval_create_from_date_string($months . " months"));
        return date_format($formatDate,"d-m-Y");
    }
}

/**
 * calcular diferencia de días entre dos fechas
 */
if (!function_exists('difference_days')) {
    function difference_days($date1, $date2) {

        $contador = date_diff(date_create($date1), date_create($date2));
        $differenceFormat = '%a';

        return $contador->format($differenceFormat); 
    }
}