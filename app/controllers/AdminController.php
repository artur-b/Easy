<?php

namespace app\controllers;

use app\models\Users as Users;
use app\models\Orders as Orders;

/**
 * Description of AdminController
 *
 * @author bzd
 */
class AdminController 
{
    public static function users()
    {
        $users = Users::getAll();
        
        $output['users'] = $users;
        echo \App::$TWIG->render("adminUserList.twig", $output);
    }
    
    public static function orders()
    {
        
    }
}
