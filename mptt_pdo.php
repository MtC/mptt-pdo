<?php

namespace MtC;

require_once 'settings.php';

use \PDO as PDO;

class MpttPDO {
    static protected $_pdo;

    static public function pdo(PDO $pdo) {
        self::$_pdo = $pdo;
    }

    static protected function isTable($sTableName) {
        $_sql   = "SELECT COUNT(*)
                   FROM information_schema.tables
                   WHERE table_schema = '".Settings::DBNAME."' AND table_name = :tableName";
        $_stmt  = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':tableName', $sTableName, PDO::PARAM_STR);
        $_stmt->execute();
        return $_stmt->fetchColumn();
    }

    static public function test() {
        if (self::isTable('test')) {
            $_sql   = "SELECT * FROM test";
            $_stmt  = self::$_pdo->query($_sql);
            $result = $_stmt->fetchAll(PDO::FETCH_OBJ);
            return $result;
        } else {
            return 'no such thing';
        }
    }
}
