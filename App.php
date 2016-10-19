<?php

use system\libs\Router as Router;
//use \libs\internal\sessions as Sessions;
use system\libs\Logger as Logger;

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
        
    private static $PUBLIC_API = [
    ]; 

    public static function start()
    {
        self::getRoute();
        self::setTwig();
//        self::checkOffline();   // show maintenance screen
        //...                
        //echo "Calling " . "\\app\\controllers\\" . self::$PAGE["controller"] . "::" . self::$PAGE["method"] . "<hr>";
        call_user_func_array("\\app\\controllers\\" . self::$PAGE["controller"] . "::" . self::$PAGE["method"], self::$PAGE["parameters"]);
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
    }

    public static function Load() 
    {
        self::getSession();
        self::setAdditionalData();        
        self::assignVariables();
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

    public static function getSession() {
        if(self::$PAGE["controller"] == "remoteCurl_v2") return;

        if(in_array(self::$PAGE['controller'].'/'.self::$PAGE['method'], self::$PUBLICAPI)) return;

        Sessions::setSettings(array("sessionName" => "kupnakreske"));
        Sessions::sessionStart();

        $customerId = ModelAuth::isLoggedCustomer();

        if($merchantId && $customerId) {
            ModelAuth::logoutCustomer();
            $customerId = false;
        }

        if($merchantId) self::$MERCHANT = ModelMerchants::getMerchantById($merchantId);
        if($customerId) self::$CUSTOMER = ModelCustomers::getCustomerById($customerId);
        
        if (!$customerId && !$merchantId && self::$PAGE["controller"] != "login") {
            self::$PAGE["controller"] = self::$DEFAULTS["controller"];
            self::$PAGE["method"]     = self::$DEFAULTS["method"];
            self::$PAGE["parameters"]     = array();            
        }
    }

    public static function setAdditionalData() {
        // Stale cookies w smarty sa zepsute. Beda poprawione w nowej wersji smarty. Trzeba cookiesy przemycic inaczej.
        self::$COOKIES = $_COOKIE;

        if(is_readable(APP_PATH . "/public/js/page/" . self::$PAGE["controller"] . ".js")) self::$PAGE["js"] = APP_URL . "/public/js/page/" . self::$PAGE["controller"] . ".js";
        if(is_readable(APP_PATH . "/public/css/page/" . self::$PAGE["controller"] . ".css")) self::$PAGE["css"] = APP_URL . "/public/css/page/" . self::$PAGE["controller"] . ".css";
    }

    public static function assignVariables() {
        self::$SMARTY->assign("PAGE", self::$PAGE);
        self::$SMARTY->assign("COOKIES", self::$COOKIES);
        self::$SMARTY->assign("CUSTOMER", self::$CUSTOMER);
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
}
