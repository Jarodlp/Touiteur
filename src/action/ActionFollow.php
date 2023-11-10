<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\user\User;

class ActionFollow extends Action {
    public function execute() : string {
        $aff ="";

        //maintenant je vérifie que l'utilisateur est bien connecté, sinon il ne peut pas follow
        if(isset($_SESSION['user'])){
            $user = unserialize($_SESSION["user"]);
            //personne qui suit
            $username = $user->username;
            //personne à suivre
            $usernameFollow = $_GET["username"];
            //si verifie que la personne à suivre est la même que l'utilisateur on affiche une erreur
            if ($username != $usernameFollow) {
                //je vérifie que l'utilisateur courant ne follow pas déjà l'auteur du tweet
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM userfollowed WHERE userfollowed.username = ?";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1,$username);
                $statement->execute();
                $followed = false;
                //on test si l'utilisateur follow déjà le username
                while($donnees = $statement->fetch()){
                    if($donnees["username"] == $username && $donnees["usernameFollowed"] == $usernameFollow){ //on compare la colonne des personnes suivies avec l'auteur du touite
                        //cas où l'utilisateur courant follow déjà l'auteur
                        $followed = true;
                    }
                }
                if(!$followed){
                    //cas où l'utilisateur courant ne follow pas déjà l'auteur
                    $query="INSERT INTO userfollowed VALUES(?, ?)";
                    $statement = $connexion->prepare($query);
                    $statement->bindParam(1,$username);
                    $statement->bindParam(2,$usernameFollow);
                    $statement->execute();
                    $aff.="C'est bon, vous suivez : ".$usernameFollow;
                }
                else {
                    $aff.="Vous suivez déjà : ".$usernameFollow;
                }
            }
            else {
                
                $aff.="Vous ne pouvez pas vous suivre";
            }
        } 
        else{
            $aff.="Vous n'êtes pas connecté, vous ne pouvez pas follow quelqu'un";
        }
        return $aff;
    }
}