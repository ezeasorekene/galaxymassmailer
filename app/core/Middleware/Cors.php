<?php

namespace ezeasorekene\App\Core\Middleware;

use ezeasorekene\App\Core\Controller as Controller;
use ezeasorekene\App\Core\System\Behaviour as Behaviour;
use Josantonius\Session\Facades\Session as Session;

class Cors 
{

    public $token;
    
    public static function token()
    {
        $token = Behaviour::generateToken();
        if (Session::has('cors_token')) {
            Session::remove('cors_token');
        }
        Session::set('cors_token', $token);
        return $token;
    }

    public static function verify(string $token)
    {
        if (Session::has('cors_token')) {
            if (Session::get('cors_token') === $token) {
                Session::remove('cors_token');
                return true;
            }
            return false;
        }
        return false;
    }

    public static function verifyRequest()
    {
        
        if (empty($_REQUEST['cors'])) {
            Behaviour::setflashMessage("Request not signed.", "danger");
            return false;
        }

        if (Session::has('cors_token')) {
            if (Session::get('cors_token') == $_REQUEST['cors']) {
                Session::remove('cors_token');
                return true;
            }
            Behaviour::setflashMessage("Request token empty.", "danger");
            return false;
        }

        
        $data = [
            'message' => 'Unauthorized request'
        ];
        $response = ['code' => 400, 'status' => 'fail', 'data' => $data];        
        if (Controller::getResponseContentType() == 'json') {
            http_response_code(400);
            echo json_encode($response);
            return false;
        }

        Behaviour::setflashMessage("You sent an unauthorized request.", "danger");
        return false;
    }

}
