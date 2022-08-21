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
        return response()->json([
            "message" => $e->getMessage(), 
            "line" => $e->getLine(), 
            "file" => $e->getFile()
        ], 500);
    }
}