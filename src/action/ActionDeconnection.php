<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;

class ActionDeconnection extends Action {
    public function execute(): string {
        $aff = "";
        Auth::unloadProfile();
        $aff.="Vous vous êtes bien déconnecté";
        return $aff;
    }
}