<?php

if (! function_exists('server_path')) {
    function server_path()
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PATH_INFO'];
    }
}