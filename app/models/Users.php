<?php

namespace app\models;

use system\libs\Db as DB;
/**
 * Description of Users
 *
 * @author bzd
 */
class Users {
    
    public static function getById($id = null)
    {
        if (empty($id)) return null;
        
        $db = DB::Connect();

        $sqlData['ID'] = $id;
        $sqlQuery = ' SELECT * '
            . ' FROM Users '
            . ' WHERE ID = :ID ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function getByEmail($email = false)
    {
        if (empty($email)) return null;
        
        $db = DB::Connect();

        $sqlData['Email'] = $email;
        $sqlQuery = ' SELECT * '
            . ' FROM Users '
            . ' WHERE Email = :Email ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
}
