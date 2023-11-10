<?php 

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;

class ActionDefault extends Action {
    public function execute () : string {
        $admin = unserialize($_SESSION['user']);
        $adminName = $admin->username;
        return "Bienvenue dans le Back Office de Touiteur ". $adminName . " <br>";
    }
}