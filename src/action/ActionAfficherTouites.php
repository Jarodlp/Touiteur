<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\TouiteRenderer;

class ActionAfficherTouites extends Action {
    public function execute () :string {
        $affichage = "";

        //données de base
        $user1 = new User("user1", "user1", "user1@mail.com", "Martin", "RONOT");
        $user2 = new User("user2", "user2", "user2@mail.com", "Jarod", "TOUSSAINT");
        $user3 = new User("user3", "user3", "user3@mail.com", "Tibère", "LE NALINEC");

        $touite1 = $user1->publieTouite("Bonjour", ["b"]);
        $touite2 = $user1->publieTouite("Aurevoir", ["a"]);
        $touite3 = $user2->publieTouite("Salut", ["s"]);

        $touite1render = new TouiteRenderer($touite1);
        $touite2render = new TouiteRenderer($touite2);
        $touite3render = new TouiteRenderer($touite3);
        print $touite1render->render(1);
        print $touite2render->render(2);
        print $touite3render->render(2);

        return $affichage;
    }
}