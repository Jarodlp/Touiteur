<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\touite\Tag;
use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTouites extends Action {
    public function execute () :string {
        $affichage = "";

        //on affiche un seul touite ou sinon tout les touites
        if (isset($_GET["id"])) {
            //on récupère le touite dans la BD et on l'affiche avec un lien par tags et le lien de l'utilisateur
        }
        else {
            //on récupère les touites de la BD
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT * FROM touite";
            $result = $connexion->query($query);
            while ($data = $result->fetch()) {
                $texte = $data["text"];
                $username = $data["username"];
                $tags = [];
                //récupère les tags du texte du touite
                $connexion2 = ConnectionFactory::makeConnection();
                $query2 = "SELECT title FROM tag 
                INNER JOIN touitetag ON tag.idTag = touitetag.idTag
                INNER JOIN touite ON touite.idTouite = touitetag.idTouite
                WHERE touite.idTouite = ?";
                $statment = $connexion2->prepare($query2);
                $statment->bindParam(1, $data["id"]);
                $statment->execute();
                while ($data2 = $statment->fetch()) {
                    var_dump($data2);
                    $tags[] = $data2["title"];
                }
                var_dump($tags);
                $touite = new Touite($texte, $username, $tags);
                $touiteRender = new TouiteRenderer($touite);
                $affichage.=$touiteRender->render(1);
                $affichage.="<a href='index.php?action=display-touites&id=".$data["id"]."'>Affichage du touite</a><br>";
            }
         }

        return $affichage;
    }
}