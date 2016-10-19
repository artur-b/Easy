<?php

namespace system\libs;

class Session 
{
    private static $_SETTINGS = array(
        "sessionName" => "default",
        "sessionExpire" => 3600,
        "sessionPath" => "/",
        "sessionDomain" => null,
        "sessionSecure" => false,
        "sessionHttpOnly" => true
    );

    public static function setAttr($settings = [])
    {
        if(is_array($settings) && !empty($settings)){
            foreach($settings as $key => $value) {
                if(isset(self::$_SETTINGS[$key])) self::$_SETTINGS[$key] = $value;
            }
        }
        else return false;
    }

    public static function start()
    {
        session_name(self::$_SETTINGS["sessionName"] . '_Session');
        session_set_cookie_params(0, self::$_SETTINGS["sessionPath"], self::$_SETTINGS["sessionDomain"], self::$_SETTINGS["sessionSecure"], self::$_SETTINGS["sessionHttpOnly"]);
        session_start();

        if (self::validateSession()) {
            if (!self::checkSessionFingerprint()) {
                $_SESSION = [];
                $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
                self::regenerateSession();
            } else if (rand(1, 100) <= 5) {
                self::regenerateSession();
            }
        } else {
            $_SESSION = [];
            session_destroy();
            session_start();
        }
    }

    private static function validateSession()
    {
        if (isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES'])) return false;
        if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time()) return false;
        return true;
    }

    private static function checkSessionFingerprint()
    {
        if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])) return false;
        if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) return false;
        if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) return false;
        return true;
    }

    private static function regenerateSession()
    {
        if (isset($_SESSION['OBSOLETE']) && $_SESSION['OBSOLETE'] == true) return;

        $_SESSION['OBSOLETE'] = true;
        $_SESSION['EXPIRES'] = time() + 10;

        session_regenerate_id(false);

        $newSession = session_id();
        session_write_close();

        session_id($newSession);
        session_start();

        unset($_SESSION['OBSOLETE']);
        unset($_SESSION['EXPIRES']);
    }

}

