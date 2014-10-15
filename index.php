<?php
require_once('pdo.php');
require_once('my_mptt_pdo.php');

use MtC\PDOConnection as CON;
use MtC\MyMpttPDO as MPTT;

$con = CON::connection();

MPTT::pdo($con);
$test = MPTT::createTable('test', true);
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
