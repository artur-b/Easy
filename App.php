<?php

use system\libs\Router as Router;
use system\libs\Session as Session;
use system\libs\Logger as Logger;

use app\models\Auth as Auth;

class App 
{
    private static $DEFAULTS = [
        'controller'    => 'Home',
        'method'        => 'start',
    ];

    private static $SYSTEM_CONTROLLERS = [
        'Home',
        'Error'
    ];
    
    public static $PAGE     = [];
    public static $COOKIES  = [];
    public static $TWIG     = null;
    
    public static $AUTH     = null;
        
    private static $PUBLIC_API = [
    ]; 

    public static function start()
    {
        self::getRoute();
        self::getSession();
        self::setTwig();
//        self::checkOffline();   // show maintenance screen                
        self::addDebug("Calling " . "\\app\\controllers\\" . self::$PAGE["controller"] . "::" . self::$PAGE["method"]);
        call_user_func_array("\\app\\controllers\\" . self::$PAGE["controller"] . "::" . self::$PAGE["method"], self::$PAGE["parameters"]);
    }
    
    public static function getRoute() 
    {
        $_route = Router::parse();
                
        if (!empty($_route['controller']) && !in_array($_route['controller'], self::$SYSTEM_CONTROLLERS)) {
            $_route['controller'] .= "Controller";
        }

        if($_route['controller'] && !$_route['method']) {
            if(method_exists("\\app\\controllers\\" . $_route["controller"], self::$DEFAULTS["method"])) {
                self::$PAGE["url"]        = APP_URL . "/" . $_route["controller"];
                self::$PAGE["controller"] = $_route["controller"];
                self::$PAGE["method"]     = self::$DEFAULTS["method"];
                self::$PAGE["parameters"] = $_route["parameters"];                
            }
            else {
                Logger::debug($_route);
                self::$PAGE["controller"] = "Error";
                self::$PAGE["method"]     = "notFound";
                self::$PAGE["parameters"] = [];
            }
        }
        else if($_route["controller"] && $_route["method"]) {            
            if(method_exists("\\app\\controllers\\" . $_route["controller"], $_route["method"])) {
                self::$PAGE["url"]        = APP_URL . "/" . $_route["controller"] . "/" . $_route["method"];
                self::$PAGE["controller"] = $_route["controller"];
                self::$PAGE["method"]     = $_route["method"];
                self::$PAGE["parameters"] = $_route["parameters"];
            }
            else {
                Logger::debug($_route);
                self::$PAGE["controller"] = "Error";
                self::$PAGE["method"]     = "notFound";
                self::$PAGE["parameters"] = [];
            }
        }
        else if(!$_route['controller'] && !$_route['method']) {
            self::$PAGE['controller'] = self::$DEFAULTS['controller'];
            self::$PAGE['method']     = self::$DEFAULTS['method'];
            self::$PAGE['parameters'] = [];
        }         
    }

    public static function checkOffline() {
        if(self::$PAGE["controller"] != "remoteCurl_v2") {
            if(self::$APP_OFFLINE && is_array(self::$APP_OFFLINE) && !empty(self::$APP_OFFLINE)) {
                if(!isset($_COOKIE[ self::$APP_OFFLINE["cookieName"] ])) die();
                else if($_COOKIE[ self::$APP_OFFLINE["cookieName"] ] != self::$APP_OFFLINE["cookieValue"]) die();
            }
        }
    }

    public static function getSession() 
    {
        Session::setAttr(['sessionName' => APP_NAME]);
        Session::start();

        //do not check Session/AUTH if public api
        if (in_array(self::$PAGE['controller'] . '/' . self::$PAGE['method'], self::$PUBLIC_API)) return;        
        
        $authId = Auth::getId();
        if (!empty($authId)) self::$AUTH = Auth::getUser($authId);
        
        if (empty($authId) && self::$PAGE['controller'] != "AuthController") {
            self::$PAGE['controller'] = self::$DEFAULTS['controller'];
            self::$PAGE['method']     = self::$DEFAULTS['method'];
            self::$PAGE['parameters'] = [];            
        }
    }

    public static function setTwig()
    {
        $loader = new \Twig_Loader_Filesystem(APP_ROOT . "/templates");
        self::$TWIG = new \Twig_Environment($loader, [
            'cache'         => APP_ROOT . "/tmp/cache",
            'debug'         => ENVIRONMENT == 'devel' ? true : false,
            'auto_reload'   => true,
        ]);
        self::$TWIG->addGlobal('APP_NAME', APP_NAME);
        self::$TWIG->addGlobal('APP_URL', APP_URL);
        
        self::$TWIG->addGlobal('session', $_SESSION);
        
        if (ENVIRONMENT == "devel") {
            self::$TWIG->addGlobal('DEBUG', 1);
        }
    }

    public static function setAdditionalData() {
        // Stale cookies w smarty sa zepsute. Beda poprawione w nowej wersji smarty. Trzeba cookiesy przemycic inaczej.
        self::$COOKIES = $_COOKIE;

        if(is_readable(APP_PATH . "/public/js/page/" . self::$PAGE["controller"] . ".js")) self::$PAGE["js"] = APP_URL . "/public/js/page/" . self::$PAGE["controller"] . ".js";
        if(is_readable(APP_PATH . "/public/css/page/" . self::$PAGE["controller"] . ".css")) self::$PAGE["css"] = APP_URL . "/public/css/page/" . self::$PAGE["controller"] . ".css";
    }

    public static function go($controller = false, $method = false, $parameters = false)
    {        
        $url = APP_URL;        
        
        if($controller && !empty($controller)) {
            $url .= "/" . $controller;
            if($method) {
                $url .= "/" . $method;
                if($parameters){
                    $url .= "/" . implode(",", $parameters);
                }
            }
        }
        header("Location: " . $url);
        exit;
    }
    
    private static function addDebug($msg)
    {
        self::$TWIG->addGlobal("DEBUG_MSG", $msg);
    }
}
