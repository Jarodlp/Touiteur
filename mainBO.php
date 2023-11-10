<?php

use iutnc\touiteur\auth\Auth;
use \iutnc\touiteurBO\dispatch\Dispatcher;

$user = unserialize($_SESSION["user"]);
if (Auth::checkUserEstAdmin($user->username)) {
    $dispatcher = new Dispatcher();
    $dispatcher->run();
} else {
    echo "vous n'êtes pas admin, vous n'avez pas accès au back office";
}