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

    static public function getTables() {
        return parent::getTables();
    }
}