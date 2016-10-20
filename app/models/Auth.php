<?php

namespace app\models;

use system\libs\Logger as Logger;

use app\models\Users as Users;

class Auth 
{
    private static $_AUTH = null;
    
    public static function authUser($login = false, $password = false) 
    {
        $user = Users::getByEmail($login);
        
        if($user) {
            if ($user['Verified'] < -1) {   // user blocked
                return false;
            }
/*
            if($customer["FirstLogin"] == 1) {                
                Logger::debug("First login detected");
                if(!self::checkPassword($password, $customer["Password"])) {
                    return false;
                }
                $hash = Common::generateHash(64);
                ModelCustomers::updateCustomerById($customer["ID"], [
                    "resetPasswordDate" => time() + 3600,
                    "resetPasswordHash" => $hash
                ]);
                Logger::debug("Redirection to ".APP_URL."/login/passRecoveryLink/".$hash);
                header("Location: " . APP_URL . "/login/passRecoveryLink/" . $hash);
                return false;
            }
 */
            if(self::checkPassword($password, $user['Password'])) {
                $_SESSION['authId'] = $user['ID'];
                return true;
            }
        }
        
        return false;
    }
    
    public static function getId() {
        if(isset($_SESSION['authId']) && $_SESSION['authId'] > 0) return $_SESSION['authId'];
        else return null;
    }
    
    public static function setId($authId) {
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
        echo "$password :: $hash<hr>";
        if(crypt($password, $hash) == $hash) {
            return true;
        }
        else return false;
    }

    /*
     
    public static function createPasswordHash($password = false) {
        if(!$password) return false;

        $cost = 10;
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        return crypt($password, $salt);
    }
*/
}