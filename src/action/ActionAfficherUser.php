<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\User;
use iutnc\touiteur\render\UserRenderer;

class ActionAfficherUser extends Action {
    public function execute () : string {
        $affichage = "";

        $db = \iutnc\touiteur\db\ConnectionFactory::makeConnection();

        $stmt1=$db->prepare("SELECT * FROM user ". 
                            "WHERE user.username=:u_username");
        $stmt1->bindParam(':u_username',$_GET['username'],\PDO::PARAM_STR);
        $stmt1->execute();

        //on a besoin que d'une seule ligne car on traîte un seul utilisateur
        $donnees=$stmt1->fetch();
        $user=new \iutnc\touiteur\user\User($donnees['username'],$donnees['password'],$donnees['email'],$donnees['firstName'],$donnees['lastName']);
        $userRenderer=new \iutnc\touiteur\render\UserRenderer($user);
        $affichage.=$userRenderer->render(1)."<br>";

        //on récupère les tweets de l'utilisateur courant affin de les afficher sur son mur
        $stmt2=$db->prepare("SELECT * FROM touite ". 
                            "WHERE touite.username=:t_username");
        $stmt2->bindParam('t_username',$user->username,\PDO::PARAM_STR);
        $stmt2->execute();
        while($donnees=$stmt2->fetch()){
            $touite=new \iutnc\touiteur\touite\Touite($donnees['text'],$donnees['username']);
            $touiteRenderer=new \iutnc\touiteur\render\TouiteRenderer($touite);
            $affichage.=$touiteRenderer->render(1);
        }

        return $affichage;
    }
}