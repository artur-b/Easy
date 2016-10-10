<?php

namespace system\libs;

class Logger
{
    public static $LOGLEVEL_ALL = 99;
    public static $LOGLEVEL_TRACE = 6;
    public static $LOGLEVEL_DEBUG = 5;
    public static $LOGLEVEL_INFO = 4;
    public static $LOGLEVEL_WARN = 3;
    public static $LOGLEVEL_ERROR = 2;
    public static $LOGLEVEL_CRITICAL = 1;
    public static $LOGLEVEL_OFF = 0;

    public static $LOGLEVEL_DEFAULT = 4;

    private static $_logLevel;
    private static $_logDir = null;
    private static $_logFileName = "log_";
    private static $_context;
    
    static private function log($logLevel, $msg, $tag = null)
    {
        if ($logLevel > self::getLogLevel()) {
            return;
        }

        $level = self::logLevelLabel($logLevel);

        if (gettype($msg) != 'string') {
            $msg = print_r($msg, 1);
        }
        $content = date('c') . " ";
        $content .= "$level ";
        if (self::$_context) {
            $content .= "[" . self::$_context . "] ";
        }
        $content .= ($tag !== '') ? "[$tag]" : '';
        $content .= ": $msg";

        list($year, $month, $day) = explode(",", date("Y,m,d"));        
        $dir = isset(self::$_logDir) ? self::$_logDir."/$year/$month/$day/" : "/tmp/$year/$month/$day/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . self::$_logFileName . date("Y-m-d") . ".log";
        
        file_put_contents($file, $content."\n", FILE_APPEND);
        chmod($file, 0777);
    }

    static public function trace($msg, $tag = '') {
        self::log(self::$LOGLEVEL_TRACE, $msg, $tag);
    }
    static public function debug($msg, $tag = '') {
        self::log(self::$LOGLEVEL_DEBUG, $msg, $tag);
    }
    static public function info($msg, $tag = '') {
        self::log(self::$LOGLEVEL_INFO, $msg, $tag);
    }
    static public function warn($msg, $tag = '') {
        self::log(self::$LOGLEVEL_WARN, $msg, $tag);
    }
    static public function error($msg, $tag = '') {
        self::log(self::$LOGLEVEL_ERROR, $msg, $tag);
    }
    static public function critical($msg, $tag = '') {
        self::log(self::$LOGLEVEL_CRITICAL, $msg, $tag);
    }

    static private function getLogLevel() {
        if (null !== self::$_logLevel) {
            return self::$_logLevel;
        }
        return self::$LOGLEVEL_DEFAULT;
    }

    public static function setLogLevel($logLevel)
    {
        self::$_logLevel = $logLevel;
    }
    
    public static function setLogDir($logDir)
    {
        self::$_logDir = $logDir;
    }
    
    public static function setLogFileName($logFileName)
    {
        self::$_logDir = $logFileName;
    }

    public static function setContext($context)
    {
        self::$_context = $context;
    }

    public static function logLevelLabel($logLevel)
    {
        $label = 'undefined';
        switch ($logLevel) {
            case self::$LOGLEVEL_TRACE:
                $label = 'TRCE';
                break;
            case self::$LOGLEVEL_DEBUG:
                $label = 'DEBG';
                break;
            case self::$LOGLEVEL_INFO:
                $label = 'INFO';
                break;
            case self::$LOGLEVEL_WARN:
                $label = 'WARN';
                break;
            case self::$LOGLEVEL_ERROR:
                $label = 'ERRO';
                break;
            case self::$LOGLEVEL_CRITICAL:
                $label = 'CRIT';
                break;
        }
        return $label;
    }
}
