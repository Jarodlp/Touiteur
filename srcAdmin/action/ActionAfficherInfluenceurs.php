<?php

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherInfluenceurs extends Action {
    public function execute () : string {

        $aff = "";
        $i = 1;

        $connexion = ConnectionFactory::makeConnection();

        $statement = $connexion->prepare('select user.username, count(userFollowed.username) as abonnes from
                                         user left join userFollowed on user.username = userFollowed.usernameFollowed
                                         group by user.username order by abonnes desc');
        $statement->execute();

        while ($result = $statement->fetch()){
            $aff .= "$i) " . $result["username"] . ", " . $result["abonnes"] . " abonnés <br>";
            $i ++;
        }

        return $aff;
    }
}