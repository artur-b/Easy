<?php

namespace app\controllers;

class Error
{
    public static function notFound()
    {
        header('HTTP/1.1 404 File not Found.', TRUE, 404);
        echo 'The URL you requested was not found.';
        exit(1); // EXIT_ERROR
    }
}
