<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\touite\Tag;
use \iutnc\deefy\db\ConnectionFactory;

class ActionAfficherTouites extends Action {
    public function execute () :string {
        $affichage = "";

        //données de base
        $user1 = new User("user1", "user1", "user1@mail.com", "Martin", "RONOT");
        $user2 = new User("user2", "user2", "user2@mail.com", "Jarod", "TOUSSAINT");
        $user3 = new User("user3", "user3", "user3@mail.com", "Tibère", "LE NALINEC");

        $touite1 = $user1->publieTouite("Bonjour", [new Tag("Ciel","Le ciel est bleu")]);

        $touite1render = new TouiteRenderer($touite1);
        
        $affichage.=$touite1render->render(1);  

        $affichage = "";

        //on récupère les touites de la BD
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM touite";
        $result = $connexion->query($query);
        while ($data = $result->fetch()) {
            $affichage.="<a href='index.php?action=display-playlist&id=".$data["id"]."'>".$data["nom"]."</a><br>";
        }

        return $affichage;
    }
}