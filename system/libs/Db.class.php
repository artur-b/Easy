<?php

namespace system\libs;

class Db
{
    private static $_DB = [];
    private static $_CONNECTIONS = [
        DB_NAME => [
            "host"     => DB_HOST,
            "port"     => DB_PORT,
            "username" => DB_USER,
            "password" => DB_PASS,
            "charset"  => "utf8"
        ]
    ];

    public static function Connect($database = DB_NAME)
    {
        if (!isset(self::$_CONNECTIONS[ $database ])) throw new \Exception("Brak zdefiniowanej bazy danych");
        else $db = self::$_CONNECTIONS[ $database ];

        if (!isset(self::$_DB[ $database ])) {
            try {
                $conn = "mysql:host=" . $db['host'] . ";dbname=" . $database . ";charset=" . $db['charset'];

                if (isset($db['port']) && !empty($db['port'])) $conn .= ";port=" . $db['port'];

                self::$_DB[ $database ] = new \PDO($conn, $db['username'], $db['password']);
                self::$_DB[ $database ]->exec("SET CHARACTER SET " . $db['charset']);
                self::$_DB[ $database ]->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                self::$_DB[ $database ]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
            }
        }
        
        return self::$_DB[ $database ];
    }
    
    public static function sqlValues($separator = false, $array = false, $mergeKeys = false)
    {
        if(!$separator || !$array) return false;

        if($mergeKeys){
            foreach($array as $key => $value){
                if(!isset($output)) $output = $key;
                else $output .= $separator.$key;
            }
        }
        else $output = implode($separator, $array);

        return $output;
    }
    
    public static function sqlSet($array = false, $mergeKeys = false)
    {
        if(!$array) return false;

        foreach($array as $key => $value){
            if($mergeKeys){
                if(!isset($output)) $output = ' '.$key.' = :'.$key.' ';
                else $output .= ' , '.$key.' = :'.$key.' ';
            }
            else {
                if(!isset($output)) $output = ' '.$value.' = :'.$value.' ';
                else $output .= ' , '.$value.' = :'.$value.' ';
            }
        }

        return $output;
    }

}
