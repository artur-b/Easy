<?php

namespace app\models;

//use \system\libs\Db as DB;
use \system\libs\Logger as Logger;

class Auth 
{
    
    public static function authUser($login = false, $password = false) 
    {
        return false;
    }
    /*
        $customer = ModelCustomers::getCustomerByEmail($login);
        
        if ($customer['Verified'] < -1) {   // user blocked
            return false;
        }

        if($customer) {
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
            if(self::checkPassword($password, $customer["Password"])) {
                $_SESSION["customerId"] = $customer["ID"];
                return true;
            }
            else return false;
        }
        else return false;
    }

    public static function createPasswordHash($password = false) {
        if(!$password) return false;

        $cost = 10;
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        return crypt($password, $salt);
    }

    public static function isLoggedCustomer() {
        if(isset($_SESSION["customerId"]) && $_SESSION["customerId"] > 0) return $_SESSION["customerId"];
        else return false;
    }

    public static function logout() {
        $_SESSION["customerId"] = 0;
        $_SESSION["merchantId"] = 0;
    }

    public static function logoutCustomer() {
        $_SESSION["customerId"] = 0;
    }

    public static function checkPassword($password = false, $hash = false) {
        if(!$password || !$hash) return false;
        if(crypt($password, $hash) == $hash) {
            return true;
        }
        else return false;
    }
*/
}