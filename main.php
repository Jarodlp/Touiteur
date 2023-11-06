<?php

require_once "vendor/autoload.php";

session_start();

use \iutnc\touiteur\dispatch\Dispatcher;
//use \iutnc\touiteur\db\ConnectionFactory;

//ConnectionFactory::setConfig('db.config.ini');

$dispatcher = new Dispatcher();
$dispatcher->run();