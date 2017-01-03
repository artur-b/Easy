<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Users as Users;
use app\models\Codes as Codes;

use app\helpers\Mail as Mail;

/**
 * Description of CodeController
 *
 * @author bzd
 */
class CodeController 
{
    public static function start()
    {
        \App::go('auth/login');
    }
    
    public static function share($code = null)
    {
        $output['code'] = $code;
        
        echo \App::$TWIG->render("share.twig", $output);
    }
    
}
