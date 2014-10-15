<?php

namespace MtC;

require_once 'settings.php';

use \PDO as PDO;

/**
 * PDOConnection
 * static class that gets its settings from settings.php
 * which is itself a static class
 */
class PDOConnection {
    static protected $con;

    /**
     * create() creates the connection and stores it in a protected $con
     */
    static protected function create() {
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

    /**
     * connection() starts create() and then returns $con
     * @return object PDO
     */
    static function connection() {
        self::create();
        return self::$con;
    }
}
