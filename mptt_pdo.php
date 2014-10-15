<?php

namespace MtC;

date_default_timezone_set('CET');
require_once 'settings.php';

use \PDO as PDO;

class MpttPDO {
    static protected $_pdo;
    static protected $_tableName;

    static protected function pdo(PDO $pdo) {
        self::$_pdo = $pdo;
    }

    static protected function logMessage($aMessage) {
        $fileName = 'phplogs/phplogs'.\date('d-m-Y').'.csv';
        $fp       = fopen($fileName, 'a');
        \fputcsv($fp, $aMessage);
        \fclose($fp);
    }

    static protected function setTable($sTableName) {
        $sTableName = \strtolower($sTableName);
        $sTableName = \substr($sTableName, 0, 5) != 'mptt_' ? 'mptt_'.$sTableName : $sTableName;
        self::$_tableName = $sTableName;
        return $sTableName;
    }

    static protected function isTable($sTableName) {
        $sTableName = self::setTable($sTableName);
        $_sql   = "SELECT COUNT(*)
                   FROM information_schema.tables
                   WHERE table_schema = '".Settings::DBNAME."' AND table_name = :tableName";
        $_stmt  = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':tableName', $sTableName, PDO::PARAM_STR);
        $_stmt->execute();
        $return = $_stmt->fetchColumn();
        return $return;
    }

    static protected function createTable($sTableName, $bAutocreate = false) {
        $bTable = self::isTable($sTableName);
        if ($bTable) {
            $message  = [\date('d-m-Y H:i:s', time()), 'The table '.self::$_tableName.' already exists.'];
            self::logMessage($message);
            return false;
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

    static protected function getTables() {
        $_sql    = "SELECT TABLE_NAME
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='".Settings::DBNAME."'
                    ORDER BY TABLE_NAME";
        $_stmt   = self::$_pdo->prepare($_sql);
        $_stmt->execute();
        $_tables = $_stmt->fetchAll(PDO::FETCH_ASSOC);
        $result  = array();
        foreach ($_tables as $_table) {
            if (substr($_table['TABLE_NAME'], 0, 5) == 'mptt_') {
                $result[] = $_table['TABLE_NAME'];
            }
        }
        return $result;
    }
}
