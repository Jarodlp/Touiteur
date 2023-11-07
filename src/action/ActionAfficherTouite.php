<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\touite\Tag;
use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTouite extends Action {
    public function execute () :string {
        $affichage = "";
        
        //on affiche un seul touite ou sinon tout les touites
        if (isset($_GET["id"])) {
            //on récupère le touite dans la BD grâce à son id et on l'affiche avec un lien par tags et le lien de l'utilisateur
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT * FROM touite WHERE idTouite = ?";
            $statment = $connexion->prepare($query);
            $statment->bindParam(1, $_GET["id"]);
            $statment->execute();
            $data = $statment->fetch();

            $texte = $data["text"];
            $username = $data["username"];
            $tags = [];
            //récupère les tags du texte du touite
            $query2 = "SELECT title FROM tag 
            INNER JOIN touitetag ON tag.idTag = touitetag.idTag
            INNER JOIN touite ON touite.idTouite = touitetag.idTouite
            WHERE touite.idTouite = ?";
            $statment = $connexion->prepare($query2);
            $statment->bindParam(1, $data["idTouite"]);
            $statment->execute();
            while ($data2 = $statment->fetch()) {
                $tags[] = $data2["title"];
            }

            $touite = new Touite($data["idTouite"], $texte, $username, $tags);
            $touiteRender = new TouiteRenderer($touite);
            $affichage.=$touiteRender->render(2);
        }
        else {
            //on récupère tout les touites de la BD
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT * FROM touite ORDER BY dateTouite DESC";
            $result = $connexion->query($query);
            while ($data = $result->fetch()) {
                $texte = $data["text"];
                $username = $data["username"];
                $tags = [];
                //récupère les tags du texte du touite
                $query2 = "SELECT title FROM tag 
                INNER JOIN touitetag ON tag.idTag = touitetag.idTag
                INNER JOIN touite ON touite.idTouite = touitetag.idTouite
                WHERE touite.idTouite = ?";
                $statment = $connexion->prepare($query2);
                $statment->bindParam(1, $data["idTouite"]);
                $statment->execute();
                while ($data2 = $statment->fetch()) {
                    $tags[] = $data2["title"];
                }

                $touite = new Touite($data["idTouite"], $texte, $username, $tags);
                $touiteRender = new TouiteRenderer($touite);
                $affichage.=$touiteRender->render(1);
            }
         }

        return $affichage;
    }
}