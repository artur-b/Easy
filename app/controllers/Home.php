<?php

namespace app\controllers;

class Home
{
    public static function start()
    {
        echo \App::$TWIG->render("home.twig");
    }    
}
