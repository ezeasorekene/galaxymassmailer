<?php

use ezeasorekene\App\Core\Controller;

class Landing extends Controller
{

    public function index()
    {
        $this->view('landing/index');
    }
    
}