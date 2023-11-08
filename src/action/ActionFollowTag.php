<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\user\User;

class ActionFollowTag extends Action{
    public function execute(): String{
        $aff="";

        //maintenant je vérifie que l'utilisateur est bien connecté, sinon il ne peut pas follow
        if(isset($_SESSION['user'])){
            $user = unserialize($_SESSION["user"]);
            //user qui follow le tag
            $username = $user->username;
            //tag à suivre
            $tagNameFollowed=$_GET['tagName'];

            //avant de commencer la vérif, nous récupèrons l'id du tag
            $connexion = ConnectionFactory::makeConnection();
            $query="SELECT idTag FROM tag ". 
                    "WHERE tag.title = ?";
            $statement = $connexion->prepare($query);
            $statement->bindParam(1,$tagNameFollowed);
            $statement->execute();
            $donnees = $statement->fetch();
            $idTag=$donnees["idTag"];

            //maintenant je vérifie que l'utilisateur courant ne follow pas déjà le tag
            $query = "SELECT * FROM tagfollowed WHERE tagfollowed.username = ?";
            $statement = $connexion->prepare($query);
            $statement->bindParam(1,$username);
            $statement->execute();

            $followed = false;
            
            while($donnees = $statement->fetch()){
                if($donnees["username"] == $username && $donnees["idTag"] == $idTag){
                    //cas où l'utilisateur courant follow déjà l'auteur
                    $followed = true;
                }
            }
            if($followed == false){
                //cas où l'utilisateur courant ne follow pas déjà l'auteur
                $query="INSERT INTO tagfollowed values(?,?)";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1,$username);
                $statement->bindParam(2,$tagNameFollowed);
                $statement->execute();
                $aff.="C'est bon, vous suivez : ".$tagNameFollowed;
            } else{
                $aff.="Vous suivez déjà : ".$tagNameFollowed;
            }
        } else{
            $aff.="Veuillez vous connecter avant de vouloir follow un tag";
        }
        return $aff;
    }
}