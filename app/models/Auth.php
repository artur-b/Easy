<?php

namespace app\models;

use app\models\Users as Users;

class Auth 
{
    private static $_AUTH   = 0;
    private static $_ADMIN  = false;
    private static $_FB     = null;

    private static $ROLE_ADMIN = 2;
    
    public static function authUser($login = false, $password = false) 
    {
        $user = Users::getByEmail($login);
        
        if(!empty($user)) {
            if ($user['Verified'] < -1) {   // user blocked
                return false;
            }

            if(self::checkPassword($password, $user['Password'])) {
                self::$_AUTH = $user['ID'];
                if ($user['Role'] == self::$ROLE_ADMIN) {
                    self::$_ADMIN = true;
                }
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

            self::$_AUTH = $user['ID'];
            if ($user['Role'] == self::$ROLE_ADMIN) {
                self::$_ADMIN = true;
            }
            self::setId($user['ID']);
            return true;
        }
        
        return false;
    }
    
    public static function getId() 
    {
        if(isset($_SESSION['authId']) && $_SESSION['authId'] > 0) return $_SESSION['authId'];
        else return null;
    }
    
    public static function setId($authId) 
    {
        $_SESSION['authId'] = $authId;        
    }
    
    public static function getUser($authId = null)
    {
        return Users::getById($authId);
    }

    public static function logout() 
    {
        $_SESSION['authId'] = 0;
        self::$_AUTH = 0;
    }

    public static function checkPassword($password = false, $hash = false) 
    {
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
    
    public static function isAdmin()
    {
        if (empty(self::$_ADMIN)) {
            $authId = (isset($_SESSION['authId']) && $_SESSION['authId'] > 0) ? $_SESSION['authId'] : 0;
            $user = Users::getById($authId);
            if (isset($user['Role']) && $user['Role'] == self::$ROLE_ADMIN) {
                self::$_ADMIN = true;
            }
        }
        return self::$_ADMIN;
    }
    
}
