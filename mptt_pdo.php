<?php

namespace MtC;

use \PDO as PDO;

class MpttPDO {
    static $_pdo;

    static public function pdo(PDO $pdo) {
        self::$_pdo = $pdo;
    }

    static public function test() {
        $_sql   = "SELECT * FROM test";
        $_stmt  = self::$_pdo->query($_sql);
        $result = $_stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}
