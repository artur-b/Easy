<?php

use system\libs\Logger as Logger;

$_appFolder     = "/app";
$_systemFolder  = "/system";

$_CONF = cc_getConfig();

define('ENVIRONMENT', isset($_CONF['ENVIRONMENT']) ? $_CONF['ENVIRONMENT'] : 'devel');

switch (ENVIRONMENT) {
    case 'devel':
        error_reporting(-1);
        ini_set('display_errors', 1);
    break;

    case 'test':
    case 'prod':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
    break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}

define("APP_ROOT", isset($_CONF['APP_ROOT']) ? $_CONF['APP_ROOT'] : $_SERVER['DOCUMENT_ROOT']);
define("APP_PATH", APP_ROOT . $_appFolder);

define("APP_NAME", isset($_CONF['APP_NAME']) ? $_CONF['APP_NAME'] : "CodeCompass App");

define("DB_HOST", $_CONF['DB_HOST']);
define("DB_PORT", $_CONF['DB_PORT']);
define("DB_NAME", $_CONF['DB_NAME']);
define("DB_USER", $_CONF['DB_USER']);
define("DB_PASS", $_CONF['DB_PASS']);

date_default_timezone_set('Europe/Warsaw');
setlocale(LC_TIME, "pl_PL.utf8");

spl_autoload_register("cc_autoload");

/**
 * TODO errors and exceptions logs
 */

/**
 * Composer packages
 */
require_once(APP_ROOT . "/vendor/autoload.php");

/**
 * Logger settings
 */
Logger::setLogDir(APP_ROOT . "/log");
if(ENVIRONMENT == 'devel') {
    Logger::setLogLevel(Logger::$LOGLEVEL_DEBUG);
}

/**
 * Functions
 */
function cc_autoload($className)
{
    $filename = APP_ROOT . "/" . str_replace("\\", "/", $className);
    if (is_readable($filename . ".class.php")) {
        require_once($filename . ".class.php") ;
    } elseif (is_readable($filename . ".php")) {
        require_once($filename . ".php");
    }
}

function cc_getConfig() 
{
    $_env = [];
    $dir = __DIR__ . "/..";
    if (is_readable($dir . "/.env")) {
        $file = fopen($dir . "/.env", "r");
        while($line = fgets($file)) {
            if (!preg_match('/^.+=.+$/', $line) || preg_match('/^[;#].*/', $line)) {
                continue;
            }
            list($k, $v) = explode('=', trim($line));
            $_env[$k] = $v;
        }
        fclose($file);
    }
    return $_env;
}
