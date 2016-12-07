<?php

namespace app\controllers;

use app\models\Users as Users;
use app\models\Orders as Orders;

use app\models\Auth as Auth;

/**
 * Description of AdminController
 *
 * @author bzd
 */
class AdminController 
{
    public static function start()
    {
        self::dashboard();
    }
    
    public static function dashboard()
    {
        self::checkRole();
        
        echo \App::$TWIG->render("admin/dashboard.twig");
    }

    public static function users()
    {
        self::checkRole();
        
        $users = Users::getAll();
        
        $output['users'] = $users;
        echo \App::$TWIG->render("admin/userList.twig", $output);
    }
    
    public static function orders()
    {
        self::checkRole();

        $orders = Orders::getAll();
        
        $output['orders'] = $orders;
        echo \App::$TWIG->render("admin/orderList.twig", $output);        
    }
    
    public static function checkRole()
    {
        if (!Auth::isAdmin()) {
            \App::go("");
        }
    }
}
