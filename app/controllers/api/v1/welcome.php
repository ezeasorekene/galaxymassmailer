<?php
use ezeasorekene\App\Core\Middleware\Request;
use ezeasorekene\App\Core\Controller as Controller;


class Welcome extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        Request::apiResponse([
            'code' => 200,
            'message' => 'API version 1 is healthy',
        ], [
                'code' => 200,
                'methods' => 'GET, HEAD, OPTIONS',
            ]);
        return;
    }




}
