<?php

namespace MtC;

use \PDO as PDO;

class PDOConnection {
    const TYPE     = 'mysql';
    const HOST     = 'localhost';
    const DBNAME   = 'mptt_server';
    const USERNAME = 'root';
    const PASSWORD = '';
    static protected $con;

    static function create() {
        try {
            self::$con = new PDO(self::TYPE.':host='.self::HOST.';dbname='.self::DBNAME, self::USERNAME, self::PASSWORD);
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
