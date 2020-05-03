<?php

use eftec\PdoOne;

include "vendor/autoload.php";

$pdo=new PdoOne('mysql','127.0.0.1','root','abc.123','example_editable_grid');
$pdo->logLevel=3;
$pdo->render();