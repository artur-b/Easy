<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Users as Users;
use app\models\Codes as Codes;

use app\helpers\Mail as Mail;

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
    
    public static function edit($userId = null)
    {
        $user = Users::getById($userId);
        
        $output['msg']['warning'] = "completeForm";
        $output['user'] = $user;
        echo \App::$TWIG->render("userEdit.twig", $output);
    }
    
    public static function invite()
    {
        $output = [];
        
        $user = Auth::getUser(Auth::getId());
        $code = Codes::getByUserId($user['ID']);
        
        if (!empty($_POST['inviteEmail'])) {
            $output['msg']['success'] = "mailSent";
                        
            $body = "<h3>Witaj!</h3>";
            $body .= "Wykorzystaj kod od " . $user['Name'] ."<br/>";
            $body .= "<a href=\"" . APP_URL . "/order/create/" . $code['UserKey'] . "\">Kliknij</a>";
            
            Mail::SetProperty("subj", "Kod zniżkowy");
            Mail::SetProperty("fnam", "BOC");
            Mail::SetProperty("body", $body);

            if (!Mail::Send($_POST['inviteEmail'])) {
                Logger::debug("Sending error: " . Mail::Adv()->ErrorInfo);
            } else {
                Logger::info("Discount code email sent OK");
            }            
        }
        $output['user'] = $user;
        $output['user']['Key'] = $code['UserKey'];
        echo \App::$TWIG->render("dashboard.twig", $output);        
    }
}
