<?php

namespace app\models;

use system\libs\Db as DB;
/**
 * Description of Codes
 *
 * @author bzd
 */
class Codes {
    
    private static $_FACTORY = null;
    private static $_GENERATOR = null;

    public static function getByKey($key = null)
    {
        if (empty($key)) return null;
        
        $db = DB::Connect();

        $sqlData['UserKey'] = $key;
        $sqlQuery = ' SELECT * FROM Codes WHERE UserKey = :UserKey ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function getByUserId($userId = null)
    {
        if (empty($userId)) return null;
        
        $db = DB::Connect();

        $sqlData['UserId'] = $userId;
        $sqlQuery = ' SELECT * FROM Codes WHERE UserId = :UserId ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
           
    public static function create($userId = null) {
        if(empty($userId)) return null;

        $db = DB::Connect();
        
        $key = self::genKey();
        
        $sqlData = [
            'UserId'    => $userId,
            'UserKey'       => $key,
            'Valid'     => 1
        ];

        $sqlNames  = ' ( ' . DB::sqlValues(", ", $sqlData, true) . ' ) ';
        $sqlValues = ' ( :' . DB::sqlValues(", :", $sqlData, true) . ' ) ';

        $sqlQuery = ' INSERT INTO Codes ' . $sqlNames . ' VALUES ' . $sqlValues . ' ';

        $sqlAction = $db->prepare($sqlQuery);

        try {
            $db->beginTransaction();
            $sqlAction->execute($sqlData);
            //$id = $db->lastInsertId();
            $db->commit();
            return $key;
        }
        catch(\PDOExecption $e) {
            $db->rollback();
            return null;
        }
    }

    public static function updateById($id = false, $sqlData = false)
    {
        if(!$id || !is_numeric($id)) return false;
        if(!$sqlData || !is_array($sqlData)) return false;

        $db = DB::Connect();

        $sqlSet   = DB::sqlSet($sqlData, true);
        $sqlData["ID"] = $id;

        $sqlQuery = ' UPDATE Users SET ' . $sqlSet . ' WHERE ID = :ID ';
        $sqlAction = $db->prepare($sqlQuery);

        try {
            $db->beginTransaction();
            $sqlAction->execute($sqlData);
            $db->commit();
        } catch (\PDOExecption $e) {
            $db->rollback();
            return false;
        }
        return $sqlAction->rowCount();
    }
    
    private static function setGenerator()
    {
        if (empty(self::$_FACTORY)) self::$_FACTORY = new \RandomLib\Factory;
        if (empty(self::$_GENERATOR)) {
            self::$_GENERATOR = self::$_FACTORY->getMediumStrengthGenerator();
        }
    }
    
    private static function genKey()
    {
        self::setGenerator();
        $key = self::$_GENERATOR->generateString(10, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        
        return $key;
    }
}
