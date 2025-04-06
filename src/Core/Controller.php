<?php

namespace App\Core;

class Controller
{
    protected $globalData = ['title' => 'Fontier'];

    function view(string $view, array $data = [])
    {
        $data = array_merge($this->globalData, $data);
        View::render($view, $data);
    }

    function json(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
