<?php

namespace app\controllers;

use \app\models\Auth as Auth;

class AuthController
{
    public static function start()
    {
        self::login();
    }
    
    public static function login()
    {                
        echo \App::$TWIG->render('login.twig', []);
        exit;
    }
    
    public static function signin()
    {
        $_POST['login'] = strtolower(trim($_POST['login']));
        $_POST['password'] = trim($_POST['password']);

        if(Auth::authUser($_POST['login'], $_POST['password'])) {            
            \App::go("user/dashboard");
        }
        else {            
            if(isset($_POST['email'])) $output['email'] = $_POST['email'];
            $output['error'] = "wrongCredentials";
            echo \App::$TWIG->render("login.twig", $output);
            exit;
        }
    }
    
    public static function logout()
    {   
        Auth::logout();
        \App::go("");
    }

}