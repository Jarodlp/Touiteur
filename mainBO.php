<?php

require_once "vendor/autoload.php";

session_start();

use iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteurBO\dispatch\Dispatcher;

ConnectionFactory::setConfig('db.config.ini');

$dispatcher = new Dispatcher();
$dispatcher->run();