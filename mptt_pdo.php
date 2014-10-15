<?php

namespace MtC;

require_once 'settings.php';

use \PDO as PDO;

class MpttPDO {
    static protected $_pdo;
    static protected $_tableName;

    static protected function pdo(PDO $pdo) {
        self::$_pdo = $pdo;
    }

    static protected function isTable($sTableName) {
        $sTableName = \strtolower($sTableName);
        $sTableName = \substr($sTableName, 0, 5) != 'mptt_' ? 'mptt_'.$sTableName : $sTableName;
        $_sql   = "SELECT COUNT(*)
                   FROM information_schema.tables
                   WHERE table_schema = '".Settings::DBNAME."' AND table_name = :tableName";
        $_stmt  = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':tableName', $sTableName, PDO::PARAM_STR);
        $_stmt->execute();
        $return = $_stmt->fetchColumn();
        self::$_tableName = $sTableName;
        return $return;
    }

    static protected function createTable($sTableName, $bAutocreate = false) {
        $bTable = self::isTable($sTableName);
        if ($bTable) {
            echo 'the table ('.self::$_tableName.') already exists';
        } else if ($bAutocreate) {
            echo 'creating '.self::$_tableName;
            $_sql = "SET NAMES utf8;
                     SET foreign_key_checks = 0;
                     SET time_zone = '+00:00';
                     SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
                     CREATE TABLE `".self::$_tableName."` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(50) NOT NULL,
                          `lft` int(11) NOT NULL,
                          `rgt` int(11) NOT NULL,
                          PRIMARY KEY (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $_stmt = self::$_pdo->prepare($_sql);
            $_stmt->execute();
        } else {
            echo 'nothing happens, the table doesn\'t exist but isn\'t forced';
        }
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
