<?php

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTendances extends Action {
    public function execute () : string {

        $aff = "";
        $i = 1;

        $connexion = ConnectionFactory::makeConnection();

        $statement = $connexion->prepare('SELECT title, COUNT(idTouite) AS nbOccurence FROM tag 
                                         LEFT JOIN touiteTag ON tag.idTag = touitetag.idTag
                                         GROUP BY title
                                         ORDER BY nbOccurence ASC');
        $statement->execute();

        while ($result = $statement->fetch()){
            $aff .= "$i) " . $result["title"] . ", " . $result["nbOccurence"];
            if ($result["nbOccurence"] === 1 || $result["nbOccurence"] === 0){
                $aff .= " touite qui mentionne le tag <br>";
            } else {
                $aff .= " touites qui mentionnent le tag <br>";
            }
            $i ++;
        }

        return $aff;
    }
}