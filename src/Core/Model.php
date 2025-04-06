<?php

namespace App\Core;

class Model
{
    protected $db;

    function __construct()
    {
        $this->db = new \PDO(implode(
            DIRECTORY_SEPARATOR,
            ['sqlite:', dirname(__DIR__, 2), 'db.sqlite']
        ));
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
