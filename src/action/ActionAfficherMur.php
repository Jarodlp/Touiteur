<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\render\UserRenderer;
use iutnc\touiteur\user\User;
use iutnc\touiteur\list\ListTouite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\render\ListTouiteRenderer;

class ActionAfficherMur extends Action {
    public function execute() : string {
        $aff="";
        $affichage.=User::getMur();
        return $aff;
    }
}