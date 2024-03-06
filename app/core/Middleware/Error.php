<?php

namespace ezeasorekene\App\Core\Middleware;

use ezeasorekene\App\Core\Middleware\Request;
use ezeasorekene\App\Core\Controller;

class Error extends Controller
{

    public function notfound()
    {
        $data = ['message' => 'Page Not Found'];
        Request::response($data, 404);

        $this->view('errors/404');
    }

    public function forbidden()
    {
        $data = ['message' => 'Access forbidden'];
        Request::response($data, 403);

        $this->view('errors/403');
    }

    public function unavailable()
    {
        $data = ['message' => 'Service unavailable'];
        Request::response($data, 503);

        $this->view('errors/503', $data);
    }

    public function maintenance()
    {
        $data = ['message' => 'Down for maintenance'];
        Request::response($data, 503);

        $this->view('errors/503', $data);
    }

    public function internalerror()
    {
        $data = ['message' => 'Internal error occured'];
        Request::response($data, 503);

        $this->view('errors/503', $data);
    }

    protected function customerror(string $message, int $code)
    {
        $data = ['message' => $message];
        Request::response($data, $code);

        $this->view("errors/{$code}", $data);
    }

    public static function errorOccured(string $message = null, int $code = null)
    {
        if (empty($message)) {
            $message = "Internal error occured";
        }
        if (empty($code)) {
            $code = 503;
        }

        $error = new Error;
        return $error->customerror($message, $code);
    }

}