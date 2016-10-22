<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Orders as Orders;

//use app\helpers\Mail as Mail;

/**
 * Description of OrderController
 *
 * @author bzd
 */
class OrderController 
{
    public static function start()
    {
        // if $authenticated -> /user/dashboard
        \App::go('auth/login');
    }
    
    public static function create($key)
    {
        // for testing purposes, mae sure we are out od auth session
        Auth::logout();
        
        $output['code'] = $key;
        echo \App::$TWIG->render("newOrder.twig", $output);
        exit;
    }

    public static function register()
    {        
        if (!empty($_POST)) {
            $_POST['name'] = trim($_POST['name']);
            $_POST['email'] = strtolower(trim($_POST['email']));
            $_POST['pesel'] = trim($_POST['pesel']);
            $_POST['phone'] = trim($_POST['phone']);
            $_POST['cruise'] = trim($_POST['cruise']);
            $_POST['code'] = trim($_POST['code']);
                
            $nameArr = explode(" ", $_POST['name']);
            foreach ($nameArr as $n) {
                $n = ucfirst(strtolower($n));
            }
            $name = implode(" ", $nameArr);
            
            // TODO - check for duplicates
                        
            $id = Orders::create([
                'CustomerName'      => $name,
                'CustomerEmail'     => $_POST['email'],
                'CustomerPhone'     => $_POST['phone'],
                'CustomerPesel'     => $_POST['pesel'],
                'CruiseId'          => $_POST['cruise'],
                'Code'              => $_POST['code'],
            ]);
        }
        if ($id) {
            echo \App::$TWIG->render("registerOrder.twig");
        } else {
            echo \App::$TWIG->render("registerOrder.twig");
        }
    }

}
