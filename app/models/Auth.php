<?php

namespace app\models;

use app\models\Users as Users;

class Auth 
{
    private static $_AUTH = null;
    private static $_FB = null;
    
    public static function authUser($login = false, $password = false) 
    {
        $user = Users::getByEmail($login);
        
        if(!empty($user)) {
            if ($user['Verified'] < -1) {   // user blocked
                return false;
            }

            if(self::checkPassword($password, $user['Password'])) {
                self::setId($user['ID']);
                return true;
            }
        }
        
        return false;
    }

    public static function authFbUser($login = false) 
    {
        $user = Users::getByEmail($login);
        
        if(!empty($user)) {
            if ($user['Verified'] < -1) {   // user blocked
                return false;
            }

            self::setId($user['ID']);
            return true;
        }
        
        return false;
    }
    
    public static function getId() {
        if(isset($_SESSION['authId']) && $_SESSION['authId'] > 0) return $_SESSION['authId'];
        else return null;
    }
    
    private static function setId($authId) {
        $_SESSION['authId'] = $authId;        
    }
    
    public static function getUser($authId = null)
    {
        return Users::getById($authId);
    }

    public static function logout() {
        $_SESSION['authId'] = 0;
    }

    public static function checkPassword($password = false, $hash = false) {
        if(!$password || !$hash) return false;
        if(crypt($password, $hash) == $hash) {
            return true;
        }
        else return false;
    }

    private static function setFB()
    {
        if (empty(self::$_FB)) {
            self::$_FB = new \Facebook\Facebook([
                'app_id'        => FB_APP_ID,
                'app_secret'    => FB_APP_SECRET,
                'default_graph_version' => 'v2.8',
            ]);
        }
    }
    
    public static function getFB()
    {
        self::setFB();
        return self::$_FB;
    }
    
}