<?php

namespace app\models;

use system\libs\Db as DB;
/**
 * Description of Users
 *
 * @author bzd
 */
class Users 
{
    private static $uidPrefix = "bwc";
    
    public static function getAll()
    {
        $db = DB::Connect();
        
        $sqlQuery = ' SELECT u.*, c.UserKey FROM Users u JOIN Codes c ON (u.ID = c.UserID) ';
        
        foreach($db->query($sqlQuery) as $sqlRow) {
            $result[] = $sqlRow;
        }
        
        return $result;
    }
    
    public static function getById($id = null)
    {
        if (empty($id)) return null;
        
        $db = DB::Connect();

        $sqlData['ID'] = $id;
        $sqlQuery = ' SELECT * FROM Users WHERE ID = :ID ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function getByEmail($email = null)
    {
        if (empty($email)) return null;
        
        $db = DB::Connect();

        $sqlData['Email'] = $email;
        $sqlQuery = ' SELECT * FROM Users WHERE Email = :Email ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function getByPesel($pesel = null)
    {
        if (empty($pesel)) return null;
        
        $db = DB::Connect();

        $sqlData['Pesel'] = $pesel;
        $sqlQuery = ' SELECT * FROM Users WHERE Pesel = :Pesel ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function create($sqlData = false) {
        if(empty($sqlData) || !is_array($sqlData)) return 0;
               
        $sqlData['Uid'] = uniqid(self::$uidPrefix);

        $db = DB::Connect();

        $sqlNames  = ' ( ' . DB::sqlValues(", ", $sqlData, true) . ' ) ';
        $sqlValues = ' ( :' . DB::sqlValues(", :", $sqlData, true) . ' ) ';

        $sqlQuery = ' INSERT INTO Users ' . $sqlNames . ' VALUES ' . $sqlValues . ' ';

        $sqlAction = $db->prepare($sqlQuery);

        try {
            $db->beginTransaction();
            $sqlAction->execute($sqlData);
            $id = $db->lastInsertId();
            $db->commit();
            return $id;
        }
        catch(\PDOExecption $e) {
            $db->rollback();
            return 0;
        }
    }

    public static function updateById($id = false, $sqlData = false){
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

    public static function getByResetPasswordHash($hash = null) {
        if (empty($hash)) return null;
        
        $db = DB::Connect();

        $sqlData['ResetPasswordHash'] = $hash;
        $sqlQuery = ' SELECT * FROM Users WHERE ResetPasswordHash = :ResetPasswordHash ';
        
        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
}
