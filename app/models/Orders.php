<?php

namespace app\models;

use system\libs\Db as DB;
/**
 * Description of Orders
 *
 * @author bzd
 */
class Orders {
    
    public static function getById($id = null)
    {
        if (empty($id)) return null;
        
        $db = DB::Connect();

        $sqlData['ID'] = $id;
        $sqlQuery = ' SELECT * FROM Orders WHERE ID = :ID ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    public static function getByCruiseId($cruise = null)
    {
        if (empty($cruise)) return null;
        
        $db = DB::Connect();

        $sqlData['CruiseId'] = $cruise;
        $sqlQuery = ' SELECT * FROM Orders WHERE CruiseId = :CruiseId ';

        $sqlAction = $db->prepare($sqlQuery);
        $sqlAction->execute($sqlData);

        if($sqlAction->rowCount()) return $sqlAction->fetch();
        else return null;
    }
    
    // TODO - only One discount per user, check user roles
    
    public static function create($sqlData = false) {
        if(empty($sqlData) || !is_array($sqlData)) return 0;

        $db = DB::Connect();

        $sqlNames  = ' ( ' . DB::sqlValues(", ", $sqlData, true) . ' ) ';
        $sqlValues = ' ( :' . DB::sqlValues(", :", $sqlData, true) . ' ) ';

        $sqlQuery = ' INSERT INTO Orders ' . $sqlNames . ' VALUES ' . $sqlValues . ' ';

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

        $sqlQuery = ' UPDATE Orders SET ' . $sqlSet . ' WHERE ID = :ID ';
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
    
}
