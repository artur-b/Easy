<?php

namespace system\libs;

class Db
{
    private static $_DB = [];
    private static $_CONNECTIONS = [
            "default" => [
                DB_NAME => [
                    "host"     => DB_HOST,
                    "port"     => DB_PORT,
                    "username" => DB_USER,
                    "password" => DB_PASS,
                    "charset"  => "utf8"
                ]
            ]
        ];

    public static function Connect($database = DB_NAME)
    {
        if (!isset(self::$_CONNECTIONS['default'][ $database ])) throw new \Exception("Brak zdefiniowanej bazy danych");
        else $db = self::$_CONNECTIONS['default'][ $database ];

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

}
