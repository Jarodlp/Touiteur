<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\user\User;

class ActionNote extends Action {
    public function execute() : string {
        $aff ="";

        //maintenant je vérifie que l'utilisateur est bien connecté, sinon il ne peut pas like
        if(isset($_SESSION['user'])){
            $user = unserialize($_SESSION["user"]);
            $username = $user->username;
            $idTouite = $_GET["idTouite"];
            //si l'utilisateur like le touite :
            if ($_GET["note"] == "like") {
                $note = 1;
            }
            //si l'utilisateur dislike le touite :
            else if ($_GET["note"] == "dislike"){
                $note = -1;
            }
            //on teste si l'utilisateur à déjà like ou dislike
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT COUNT(*), touitenote.note FROM touitenote WHERE touitenote.username = ? AND touitenote.idTouite = ?";
            $statement = $connexion->prepare($query);
            $statement->bindParam(1, $username);
            $statement->bindParam(2, $idTouite);
            $statement->execute();
            $result = $statement->fetch();
            $notePresente = ($result[0] === 1);
            //si l'ulitilisateur à déjà like ou dislike, on modifie la table
            if ($notePresente) {
                //la note déjà présente en BD
                $previousNote = $result[1];
                //on teste si la note actuellement effectuée est la même déjà présente dans la BD
                if ($note == $previousNote) {
                    $aff.="Vous avez déjà noté ce touite";
                }
                else {
                    $query = "UPDATE touitenote SET note = ? WHERE idTouite = ? AND username = ?";
                    $statement = $connexion->prepare($query);
                    $statement->bindParam(1, $note);
                    $statement->bindParam(2, $idTouite);
                    $statement->bindParam(3, $username);
                    $statement->execute();
                    if ($note == 1) {
                        $aff.="Vous avez like ce touite";
                    }
                    else {
                        $aff.="Vous avez dislike ce touite";
                    }
                }
            }
            //sinon on ajoute dans la table le like ou dislike
            else {
                $query = "INSERT INTO touitenote VALUES (?, ?, ?)";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1, $username);
                $statement->bindParam(2, $idTouite);
                $statement->bindParam(3, $note);
                $statement->execute();
                if ($note == 1) {
                    $aff.="Vous avez like ce touite";
                }
                else {
                    $aff.="Vous avez dislike ce touite";
                }
            }
        } 
        else{
            $aff.="Vous n'êtes pas connecté, vous ne pouvez pas like le touite";
        }
        return $aff;
    }
}