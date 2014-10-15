<?php

namespace MtC;

require_once 'mptt_pdo.php';

use \PDO as PDO;

/**
 * static class MyMpttPDO creates the layer to protect the parent
 */
class MyMpttPDO extends MpttPDO {
    static public function pdo(PDO $pdo) {
        parent::pdo($pdo);
    }

    static public function createTable($sTableName, $bAutocreate = false) {
        return parent::createTable($sTableName, $bAutocreate);
    }

    static public function setTable($sTableName) {
        return parent::setTable($sTableName);
    }

    static public function getTable() {
        return parent::getTable();
    }

    static public function getTables() {
        return parent::getTables();
    }

    static public function load($iId) {
        return parent::load($iId);
    }

    static public function createNode($sName) {
        return parent::createNode($sName);
    }

    static public function addNode($soNode, $oParent) {
        return parent::addNode($soNode, $oParent);
    }

    static public function addSibling($soNode, $oSibling) {
        return parent::addSibling($soNode, $oSibling);
    }

    static public function store($oNode) {
        return parent::store($oNode);
    }
}