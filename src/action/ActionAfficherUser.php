<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\render\UserRenderer;
use iutnc\touiteur\user\User;
use iutnc\touiteur\list\ListTouite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\render\ListTouiteRenderer;

class ActionAfficherUser extends Action {
    public function execute() : string {
        $aff="";
        //on récupère l'utilisateur dans la BD grâce au paramètre username dans le GET
        $username = $_GET["username"];
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM user WHERE user.username = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $username);
        $statment->execute();
        $donnees = $statment->fetch();
        $user = new User($donnees['username'],$donnees['password'],$donnees['email'],$donnees['firstName'],$donnees['lastName']);
        $userRenderer = new UserRenderer($user);
        $aff.=$userRenderer->render(2);

        //on récupère les touites de l'utilisateur courant affin de les afficher sur son mur
        $statment = $connexion->prepare("SELECT * FROM touite WHERE touite.username = ? ORDER BY dateTouite DESC");
        $username = $user->username;
        $statment->bindParam(1,$username);
        $statment->execute();
        $listTouite = new ListTouite();
        while($donnees = $statment->fetch()){
            $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
            $listTouite->addTouite($touite);
        }
        $listTouiteRenderer = new ListTouiteRenderer($listTouite);
        $aff.=$listTouiteRenderer->render(1);
        return $aff;
    }
}