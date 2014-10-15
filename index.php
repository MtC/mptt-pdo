<?php
require_once('pdo.php');
require_once('my_mptt_pdo.php');

use MtC\PDOConnection as CON;
use MtC\MyMpttPDO as MPTT;

$con = CON::connection();

function beautify($aArray) {
    echo "<pre>";
    print_r($aArray);
    echo "</pre>";
}

MPTT::pdo($con);
MPTT::setTable('test');

$test   = MPTT::load(2);
//$test   = MPTT::addSibling('gekkie', $test);
//beautify($test);
//$test2  = MPTT::createNode('tada');
//$test2  = MPTT::addNode('subtada', $test2);
//$test2  = MPTT::addSibling('tada2', $test2);
//$test   = MPTT::addNode($test2, $test);
beautify($test);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Welcome</title>
    </head>
    <body>
      <p><?php echo "Hello PHP!"; ?></p>
    </body>
</html>
