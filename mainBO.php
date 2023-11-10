<?php

require_once "vendor/autoload.php";

use iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteurBO\dispatch\Dispatcher;

session_start();

ConnectionFactory::setConfig('db.config.ini');

    $dispatcher = new Dispatcher();
    $dispatcher->run();