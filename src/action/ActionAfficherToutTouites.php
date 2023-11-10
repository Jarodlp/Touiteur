<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\list\ListTouite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\render\ListTouiteRenderer;

class ActionAfficherToutTouites extends Action {
    public function execute() : string {
        $affichage="";
        //on récupère tous les touites de la BD
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM touite ORDER BY dateTouite DESC";
        $result = $connexion->query($query);
        $listTouite = new ListTouite();
        while ($data = $result->fetch()) {
            $touite = new Touite($data["idTouite"], $data["text"], $data["username"]);
            $listTouite->addTouite($touite);
        }
        $listTouiteRenderer = new ListTouiteRenderer ($listTouite);
        $affichage.=$listTouiteRenderer->render(1);
        return $affichage;
    }
}