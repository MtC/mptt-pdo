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
        $time     = \time();
        array_unshift($aMessage, \date('H:i:s', $time));
        $fileName = 'mpttlogs/mpttlogs'.\date('d-m-Y', $time).'.csv';
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

    static protected function getTable() {
        return self::$_tableName;
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
            $aMessage = ["The table '".self::$_tableName."' already exists."];
            self::logMessage($aMessage);
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
            $aMessage = ["The table '".self::$_tableName."' doesn't exist, but isn't enforced."];
            self::logMessage($aMessage);
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

    static protected function load($iId) {
        $_sql = "SELECT main.*, loaded.id AS id_parent
                 FROM
                     ".self::$_tableName." AS main,
                     (SELECT * FROM ".self::$_tableName." WHERE id = :id) AS loaded
                 WHERE main.lft >= loaded.lft AND main.rgt <= loaded.rgt
                 ORDER BY main.lft";
        $_stmt = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':id', $iId, PDO::PARAM_INT);
        $_stmt->execute();
        $_result = $_stmt->fetchAll(PDO::FETCH_OBJ);
        return $_result;
    }

    static protected function createMasterNode($sName) {
        //$_sql   = "SELECT rgt FROM ".self::$_tableName." ORDER BY rgt DESC LIMIT 0, 1";
        //$_stmt  = self::$_pdo->prepare($_sql);
        //$_stmt->execute();
        //$result = $_stmt->fetch(PDO::FETCH_OBJ);
        $node   = new \stdClass;
        $node->name = $sName;
        $node->lft = 1;
        $node->rgt = 2;
        return $node;
    }

    static protected function createNode($sName) {
        $_sql  = "INSERT INTO ".self::$_tableName." (name, lft, rgt)
                  SELECT :name, MAX(rgt)+1, MAX(rgt)+2 FROM ".self::$_tableName;
        $_stmt = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':name', $sName, PDO::PARAM_STR);
        $_stmt->execute();
        $id    = self::$_pdo->lastInsertId();
        return self::load($id);
    }

    static protected function updateNode($oNode) {
        $_sql  = "UPDATE ".self::$_tableName."
                  SET name = :name, lft = :lft, rgt = :rgt
                  WHERE id = :id";
        $_stmt = self::$_pdo->prepare($_sql);
        $_stmt->bindParam(':name', $oNode->name, PDO::PARAM_STR);
        $_stmt->bindParam(':lft', $oNode->lft, PDO::PARAM_INT);
        $_stmt->bindParam(':rgt', $oNode->rgt, PDO::PARAM_INT);
        $_stmt->bindParam(':id', $oNode->id, PDO::PARAM_INT);
        $_stmt->execute();
    }

    static protected function addNode($soNode, $oParent) {
        if (is_string($soNode)) {
            $soNode = self::createNode($soNode);
            $soNode[0]->lft = 1;
            $soNode[0]->rgt = 2;
        }
        foreach($soNode as $iKey => $oNode) {
            if ($iKey == 0) {
                $iLft = $oParent[0]->rgt - $oNode->lft;
            }
            $oNode->lft = $oNode->lft + $iLft;
            $oNode->rgt = $oNode->rgt + $iLft;
            $oNode->id_parent = $oParent[0]->id;
            self::updateNode($oNode);
            $oParent[]  = $oNode;
        }
        $oParent[0]->rgt = $oParent[0]->lft + (count($oParent) * 2) - 1;
        self::updateNode($oParent[0]);
        return $oParent;
    }

    static protected function addSibling($saNode, $aSibling) {
        //perhaps too difficult
        if (is_string($saNode)) {
            $saNode = self::createNode($saNode);
        }
        $iRgt  = $aSibling[count($aSibling) - 1]->rgt + 1;
        //$iLft  = $oParent[0]->rgt;
        foreach($saNode as $oNode) {
            $oNode->lft = $oNode->lft + $iRgt;
            $oNode->rgt = $oNode->rgt + $iRgt;
            $oNode->id_parent = false;
            //save
            $aSibling[]  = $oNode;
        }
        //save
        //$oParent[0]->rgt = count($oParent) * 2;
        return $aSibling;
    }

    static protected function store($oNode) {
        
    }
}
