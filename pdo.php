<?php

namespace MtC;

require_once 'settings.php';

use \PDO as PDO;

class PDOConnection {
    static protected $con;

    static function create() {
        try {
            self::$con = new PDO(
                Settings::TYPE.':host='.Settings::HOST.';dbname='.Settings::DBNAME,
                Settings::USERNAME,
                Settings::PASSWORD);
            self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    static function connection() {
        self::create();
        return self::$con;
    }
}
