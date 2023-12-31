<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\db\ConnectionFactory;

class ActionSupprimerTouite extends Action {
    public function execute() : string {
        $aff="";
        $idTouite = $_GET["id"];
        $connexion = ConnectionFactory::makeConnection();
        $query = "DELETE FROM touiteimage WHERE idTouite = ?;
        DELETE FROM touitenote WHERE idTouite = ?;
        DELETE FROM touitetag WHERE idTouite = ?;
        DELETE FROM touite WHERE idTouite = ?;";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $idTouite);
        $statment->bindParam(2, $idTouite);
        $statment->bindParam(3, $idTouite);
        $statment->bindParam(4, $idTouite);
        $statment->execute();
        $result = $statment->fetch();
        $aff.="Votre touite à bien été supprimé";
        return $aff;
    }
}