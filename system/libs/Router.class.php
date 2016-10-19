<?php

namespace system\libs;

class Router
{
    private static $DATA = [
        'controller'    => null,
        'method'        => null,
        'parameters'   => [],
    ];

    public static function parse($prefix = "")
    {
        switch ($prefix) {
            default:
            case "":
                if (isset($_GET['c'])) self::$DATA['controller'] = ucfirst($_GET['c']);
                if (isset($_GET['m'])) self::$DATA['method'] = $_GET['m'];
                if (isset($_GET['p'])) {
                    $trimmed = rtrim($_GET['p'], "/");
                    self::$DATA['parameters'] = explode(",", $trimmed);
                }
                break;
            case "admin":
                break;
        }

        return self::$DATA;
    }
    
}
