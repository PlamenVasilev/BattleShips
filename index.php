<?php
require(__DIR__.'/includes/init.php');

$app = new Application(10,10, array(5,4,4));

$app->run();