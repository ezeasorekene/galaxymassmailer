<?php

namespace ezeasorekene\App\Core;

class Controller
{
    public $data = [];
    public $view = 'dashboard';

    public function view($view, $data = [])
    {
        $view = str_replace(".", "/", $view);
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once('../app/views/' . $view . '.php');
        }
    }

    public static function setResponseHeaders(array $options = [])
    {
        $responseType = Controller::getResponseContentType();
        $origin = isset($options['origin']) ? $options['origin'] : '*';
        $methods = isset($options['methods']) ? $options['methods'] : 'GET';
        $max_age = isset($options['max_age']) ? $options['max_age'] : '3600';

        if ($responseType === 'json') {
            header("Access-Control-Allow-Origin: $origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: $methods");
            header("Access-Control-Max-Age: $max_age");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        } else {
            // default to text/html if no response type is specified or invalid
            header("Access-Control-Allow-Origin: $origin");
            header("Content-Type: text/html; charset=UTF-8");
            header("Access-Control-Allow-Methods: $methods");
            header("Access-Control-Max-Age: $max_age");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        }
    }

    public static function getResponseContentType()
    {
        if (isset($_SERVER['HTTP_RESPONSE_CONTENT_TYPE']) && strtolower($_SERVER['HTTP_RESPONSE_CONTENT_TYPE'])=='json')
        {
            return 'json';
        }
        
        if(isset($_REQUEST['content_type']) && strtolower($_REQUEST['content_type'])=='json')
        {
            return 'json';
        }

        return 'html';
    }
}