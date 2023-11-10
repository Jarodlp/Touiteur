<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\exception\AuthException;
use iutnc\touiteur\user\User;

class ActionDeconnection extends Action {
    public function execute(): string {
        $aff = "";
        unset($_SESSION["user"]);
        $aff.="Vous vous êtes bien déconnecté";
        return $aff;
    }
}