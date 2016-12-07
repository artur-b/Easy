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
        // for testing purposes, make sure we are out od auth session
        Auth::logout();
        
        $output['code'] = $key;
        echo \App::$TWIG->render("newOrder.twig", $output);
        exit;
    }

    public static function register()
    {        
        $id = null;
        
        $form = filter_input_array(INPUT_POST);
        
        if (!empty($form)) {
            $form['name'] = isset($form['name']) ? trim($form['name']) : "";
            $form['email'] = isset($form['email']) ? strtolower(trim($form['email'])) : "";
            $form['pesel'] = isset($form['pesel']) ? trim($form['pesel']) : 0;
            $form['phone'] = isset($form['phone']) ? trim($form['phone']) : "";
            $form['cruise'] = isset($form['cruise']) ? trim($form['cruise']) : "";
            $form['code'] = isset($form['code']) ? trim($form['code']) : "";
                
            $nameArr = explode(" ", $form['name']);
            foreach ($nameArr as $n) {
                $n = ucfirst(strtolower($n));
            }
            $name = implode(" ", $nameArr);
            
            // TODO - check for duplicates
                        
            $id = Orders::create([
                'CustomerName'      => $name,
                'CustomerEmail'     => $form['email'],
                'CustomerPhone'     => $form['phone'],
                'CustomerPesel'     => $form['pesel'],
                'CruiseId'          => $form['cruise'],
                'Code'              => $form['code'],
            ]);
        }
        if ($id) {
            echo \App::$TWIG->render("registerOrder.twig");
        } else {
            echo \App::$TWIG->render("registerOrder.twig");
        }
    }
    
    public static function import()
    {
    echo "<pre>";
        print_r($_FILES);
    echo "</pre>";
        if (isset($_FILES['xls'])) {
            for ($i=0; $i < count($_FILES['xls']['name']; $i++) {
                // TODO check errors
                $fileName = $_FILES['xls']['name'][$i];
                $tmpName = $FILES['xls']['tmp_name'][$i];

                echo "Loading $fileName...<br/>";
                $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
//                $objPHPEx cel = $objReader->load
            }            
        }
        exit;
//        \App::go("admin/orders");
    }

}
