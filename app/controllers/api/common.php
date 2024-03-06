<?php
use ezeasorekene\App\Core\Middleware\Request;
use ezeasorekene\App\Core\Controller as Controller;


class Common extends Controller
{

    public function index()
    {
        Request::apiResponse([
            'code' => 200,
            'message' => 'The Public API is healthy',
        ], [
                'code' => 200,
                'methods' => 'GET, HEAD',
            ]);
        return;
    }


}
