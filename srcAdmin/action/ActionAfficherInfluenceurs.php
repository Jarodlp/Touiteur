<?php

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherInfluenceurs extends Action {
    public function execute () : string {

        $aff = "";
        $i = 1;

        $connexion = ConnectionFactory::makeConnection();

        $statement = $connexion->prepare('select usernameFollowed, count(username) as abonnes from userfollowed 
                                         group by usernameFollowed order by abonnes asc');
        $statement->execute();

        while ($result = $statement->fetch()){
            $aff .= "$i) " . $result["usernameFollowed"] . ", " . $result["abonnes"] . " abonnés <br>";
            $i ++;
        }

        return $aff;
    }
}