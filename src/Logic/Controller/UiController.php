<?php

namespace App\Logic\Controller;

use App\Core\Controller;

class UiController extends Controller
{
    function main()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }
        
        $this->view('home.ui');
    }
}
