<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\User;

class ActionAfficherMur extends Action {
    public function execute() : string {
        $affichage="";
        $affichage.=User::getMur();
        return $affichage;
    }
}