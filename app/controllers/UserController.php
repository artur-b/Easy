<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Users as Users;

/**
 * Description of UserController
 *
 * @author bzd
 */
class UserController 
{
    public static function start()
    {
        // if $authenticated -> /user/dashboard
        \App::go('auth/login');
    }
    
    public static function dashboard()
    {
        $output = ['user' => Users::getById(Auth::getId())];
        echo \App::$TWIG->render("dashboard.twig", $output);
    }
}
