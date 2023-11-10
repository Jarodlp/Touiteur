<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTouite extends Action {
    public function execute () : string {
        $affichage = "";
        //on récupère le touite dans la BD grâce à son id et on l'affiche avec un lien par tags et le lien de l'utilisateur
        $idTouite = $_GET["id"];
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM touite WHERE idTouite = ?";
        $statement = $connexion->prepare($query);
        $statement->bindParam(1, $idTouite);
        $statement->execute();
        $donnees = $statement->fetch();

        $texte = $donnees["text"];
        $username = $donnees["username"];

        //récupère les tags du texte du touite
        $tags = [];
        $query = "SELECT title FROM tag 
        INNER JOIN touitetag ON tag.idTag = touitetag.idTag
        INNER JOIN touite ON touite.idTouite = touitetag.idTouite
        WHERE touite.idTouite = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $idTouite);
        $statment->execute();
        while ($donnee = $statment->fetch()) {
            $tags[] = $donnee["title"];
        }

        //on actualise la note dans le cas où l'utilisateur aurait like ou dislike
        if(isset($_GET['note'])){
            $affichage.=Touite::note($_GET['note'],$idTouite);
        }

        //on récupère le score du touite
        $query = "SELECT SUM(note) FROM touitenote WHERE idTouite = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $idTouite);
        $statment->execute();
        $result = $statment->fetch();
        $note = $result[0];
        if($note == null) {
            $note = 0;
        }

        //on récupère le chemin de l'image
        $query = "SELECT fileName FROM image
        INNER JOIN touiteimage ON touiteimage.idImage = image.idImage
        INNER JOIN touite ON touite.idTouite = touiteImage.idTouite
        WHERE touite.idTouite = ?";
        $statement = $connexion->prepare($query);
        $statement->bindParam(1, $idTouite);
        $statement->execute();
        $result = $statement->fetch();
        if ($result[0] !== "") {
            $cheminImage = $result[0];
        }
        else {
            $cheminImage = "";
        }

        $touite = new Touite($idTouite, $texte, $username, $tags, $note, $cheminImage);
        $touiteRender = new TouiteRenderer($touite);

        $affichage.=$touiteRender->render(2);
        return $affichage;
    }
}