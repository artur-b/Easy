<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Users as Users;
use app\models\Codes as Codes;

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
        $authId = Auth::getId();
        
        $code = Codes::getByUserId($authId);        
        if (empty($code)) {
            $key = Codes::create($authId);
        } else {
            $key = $code['UserKey'];
        }
        $output['user'] = Users::getById($authId);
        $output['user']['Key'] = $key;
        echo \App::$TWIG->render("dashboard.twig", $output);
    }
}
