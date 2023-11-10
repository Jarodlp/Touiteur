<?php

namespace iutnc\touiteurBO\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTendances extends Action {
    public function execute () : string {

        $aff = "";
        $i = 1;

        $connexion = ConnectionFactory::makeConnection();

        $statement = $connexion->prepare('select title, count(idTouite) as nbOccurence from
                                         tag left join touiteTag on tag.idTag = touiteTag.idTag
                                         group by title order by nbOccurence desc');
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