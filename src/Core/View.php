<?php

namespace App\Core;

class View
{
    static function render(string $view, array $data)
    {
        extract($data);
        require_once implode(
            DIRECTORY_SEPARATOR,
            [dirname(__DIR__), 'Logic', 'View', $view . '.phtml']
        );
    }
}
