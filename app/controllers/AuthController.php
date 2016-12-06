<?php

namespace app\controllers;

use system\libs\Logger as Logger;

use app\models\Auth as Auth;
use app\models\Users as Users;

use app\helpers\Mail as Mail;

class AuthController
{
    public static function start()
    {
        self::login();
    }
    
    public static function login()
    {   
        $fb = Auth::getFb();
        $helper = $fb->getRedirectLoginHelper();
        $fbLoginUrl = $helper->getLoginUrl(APP_URL . "/auth/fbCallback", ["public_profile", "email"]);
        
        echo \App::$TWIG->render('login.twig', ['fbLoginUrl' => $fbLoginUrl]);
    }
    
    public static function signin()
    {
        if (empty($_POST)) {
            self::logout();
        }
        
        $output['old'] = $_POST;
        
        $_POST['login'] = strtolower(trim($_POST['login']));
        $_POST['password'] = trim($_POST['password']);

        if(Auth::authUser($_POST['login'], $_POST['password'])) {
            if (Auth::isAdmin()) {
                \App::go("admin/dashboard");
            } else {
                \App::go("user/dashboard");
            }
        }
        else {            
            if(isset($_POST['email'])) $output['email'] = $_POST['email'];
            $output['msg']['error'] = "wrongCredentials";
            echo \App::$TWIG->render("login.twig", $output);
        }
    }
    
    public static function logout()
    {   
        Auth::logout();
        \App::go("");
    }
    
    public static function register()
    {
        $fb = Auth::getFb();
        $helper = $fb->getRedirectLoginHelper();
        $fbLoginUrl = $helper->getLoginUrl(APP_URL . "/auth/fbCallback", ["public_profile", "email"]);

        echo \App::$TWIG->render("register.twig", ['fbLoginUrl' => $fbLoginUrl]);
    }
    
    public static function signup()
    {
        $output['old'] = $_POST;
        
        $_POST['email'] = strtolower(trim($_POST['email']));
        $_POST['password'] = trim($_POST['password']);
        $_POST['name'] = trim($_POST['name']);
        $_POST['pesel'] = trim($_POST['pesel']);
        $_POST['phone'] = trim($_POST['phone']);
                
        $nameArr = explode(" ", $_POST['name']);
        foreach ($nameArr as $n) {
            $n = ucfirst(strtolower($n));
        }
        $name = implode(" ", $nameArr);
        
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $hash = hash("sha256", $_POST['email'] . time() . rand(1, strlen($name)));
        
        $user1 = Users::getByEmail($_POST['email']);
        $user2 = Users::getByPesel($_POST['pesel']);
                
        if (!empty($user1) || !empty($user2)) {        
            $output['msg']['error'] = "alreadyExists";
            echo \App::$TWIG->render("register.twig", $output);
            exit;
        }
        
        $id = Users::create([
            'Email'     => $_POST['email'],
            'Password'  => $pass,
            'Name'      => $name,
            'Pesel'     => $_POST['pesel'],
            'Phone'     => $_POST['phone'],
            'Rules'     => isset($_POST['accept']) ? 1 : 0,
            'ResetPasswordHash' => $hash,
        ]);
        
        if ($id) { 
            $body = "<h3>Witaj!</h3>";
            $body .= "Kliknij:<br/>";
            $body .= "<a href=\"" . APP_URL . "/auth/verify/" . $hash . "\">Weryfikuj</a>";
            
            Mail::SetProperty("subj", "Rejestracja");
            Mail::SetProperty("fnam", "BOC");
            Mail::SetProperty("body", $body);

            if (!Mail::Send($_POST['email'])) {
                Logger::debug("Sending error: " . Mail::Adv()->ErrorInfo);
            } else {
                Logger::info("Registration email sent OK");
            }
            $output['msg']['success'] = "checkEmail";   
        }
        else {                    
            $output['msg']['error'] = "registerFailed";
        }
        echo \App::$TWIG->render("login.twig", $output);
    }
    
    public static function verify($hash = null)
    {
        $user = Users::getByResetPasswordHash($hash);
        
        if (!empty($user)) {
            Users::updateById($user['ID'], ['Verified' => 1, 'ResetPasswordHash' => '']);
        
            Auth::setId($user['ID']);
            \App::go("user/dashboard");
        } else {
            $output['msg']['error'] = "unknownHash";
            echo \App::$TWIG->render("login.twig", $output);
            exit;
        }
    }

    public static function forgotPassword()
    {          
        $output = [];
        
        if (!empty($_POST['email'])) {
            $output['old'] = $_POST;
            $user = Users::getByEmail($_POST['email']);
            
            if (!empty($user)) {
            
                $hash = hash("sha256", $_POST['email'] . time() . rand(1, strlen($user['Name'])));
                Users::updateById($user['ID'], ['ResetPasswordHash' => $hash]);

                $body = "<h3>Witaj!</h3>";
                $body .= "Kliknij:<br/>";
                $body .= "<a href=\"" . APP_URL . "/auth/resetPassword/" . $hash . "\">Resetuj</a>";

                Mail::SetProperty("subj", "Reset hasła");
                Mail::SetProperty("fnam", "BOC");
                Mail::SetProperty("body", $body);

                if (!Mail::Send($_POST['email'])) {
                    Logger::debug("Sending error: " . Mail::Adv()->ErrorInfo);
                } else {
                    Logger::info("Reset email sent OK");
                }
                $output['msg']['success'] = "checkEmail";                               
            } else {
                $output['msg']['error'] = "unknownEmail";
            }
        }        
        echo \App::$TWIG->render("forgot.twig", $output);
    }
    
    public static function resetPassword($hash = null)
    {
        $user = Users::getByResetPasswordHash($hash);
        
        // todo check also date
        
        if (!empty($user)) {
            $output['hash'] = $hash;
            echo \App::$TWIG->render("reset.twig", $output);            
        } else {
            $output['msg']['error'] = "unknownHash";
            echo \App::$TWIG->render("login.twig", $output);
        }                
    }
    
    public static function updatePassword()
    {
        if (empty($_POST['password']) || empty($_POST['confirm']) || strcmp($_POST['password'], $_POST['confirm']) != 0) {
            $output['hash'] = $_POST['hash'];
            $output['msg']['error'] = "unknownHash";
            echo \App::$TWIG->render("reset.twig", $output);
            exit;
        }
        
        $user = Users::getByResetPasswordHash($_POST['hash']);        
        if (!empty($user)) {
            $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
            Users::updateById($user['ID'], ['Password' => $pass, 'ResetPasswordHash' => '']);
            
            $output['msg']['success'] = "changedPassword";
            echo \App::$TWIG->render("login.twig", $output);
        } else {
            $output['msg']['error'] = "unknownHash";
            echo \App::$TWIG->render("login.twig", $output);
        }                
    }

    public static function fbCallback()
    {
        $fb = Auth::getFB();
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            return false;
        }
        if (isset($accessToken)) {
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            $fb->setDefaultAccessToken((string) $accessToken);
         
            try {
                $response = $fb->get('/me?fields=name,email');
                $user = $response->getGraphUser();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo '<p>Graph returned an error:</p> ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo '<p>Facebook SDK returned an error:</p> ' . $e->getMessage();
                exit;
            }
            if (Auth::authFbUser($user['email'])) {
                \App::go("user/dashboard");
            } else {
                $id = Users::create([
                    'Email'     => $user['email'],
                    'Password'  => '',
                    'Name'      => $user['name'],
                    'Verified'  => 1,
                ]);
                Auth::authFbUser($user['email']);
                \App::go("user/edit/" . $id);
            }
        }
    }
    
}