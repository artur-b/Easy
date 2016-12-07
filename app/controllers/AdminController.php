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
    public static function start()
    {
        self::dashboard();
    }
    
    public static function dashboard()
    {
        echo \App::$TWIG->render("adminDashboard.twig");
    }

    public static function users()
    {
        $users = Users::getAll();
        
        $output['users'] = $users;
        echo \App::$TWIG->render("adminUserList.twig", $output);
    }
    
    public static function orders()
    {
        $orders = Orders::getAll();
        
        $output['orders'] = $orders;
        echo \App::$TWIG->render("admin/OrderList.twig", $output);
        
    }
}
