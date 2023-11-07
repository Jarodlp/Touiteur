<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\User;
use iutnc\touiteur\render\UserRenderer;

class ActionAfficherUser extends Action {
    public function execute () : string {
        $affichage = "";

        //données de base
        $user1 = new User("user1", "user1", "user1@mail.com", "Martin", "RONOT");
        $user2 = new User("user2", "user2", "user2@mail.com", "Jarod", "TOUSSAINT");
        $user3 = new User("user3", "user3", "user3@mail.com", "Tibère", "LE NALINEC");

        $user1render = new UserRenderer($user1);
        $user2render = new UserRenderer($user2);
        $user3render = new UserRenderer($user3);
        $affichage.=$user1render->render(1);
        $affichage.=$user2render->render(2);
        $affichage.=$user3render->render(2);

        return $affichage;
    }
}