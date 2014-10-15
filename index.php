<?php
require_once('pdo.php');
require_once('mptt_pdo.php');

use MtC\PDOConnection as CON;
use MtC\MpttPDO as MPTT;

$con = CON::connection();

MPTT::pdo($con);

print_r(MPTT::test());
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
