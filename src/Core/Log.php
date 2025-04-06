<?php

namespace App\Core;

class Log
{
    static function toFile(mixed $data)
    {
        file_put_contents(
            APP_LOG_FILE,
            json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL,
            FILE_APPEND,
        );
    }
}
