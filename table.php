<?php

use eftec\bladeone\BladeOne;

include "vendor/autoload.php";

$blade=new BladeOne();


echo $blade->run('table',[]);