<?php

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherInfluenceurs extends Action {
    public function execute () : string {

        $aff = "";
        $i = 1;

        $connexion = ConnectionFactory::makeConnection();

        $statement = $connexion->prepare('SELECT user.username, COUNT(userFollowed.username) AS abonnes FROM
                                         user LEFT JOIN userFollowed ON user.username = userFollowed.usernameFollowed
                                         GROUP BY user.username ORDER BY abonnes DESC');
        $statement->execute();

        while ($result = $statement->fetch()){
            $aff .= "$i) " . $result["username"] . ", " . $result["abonnes"] . " abonnés <br>";
            $i ++;
        }

        return $aff;
    }
}