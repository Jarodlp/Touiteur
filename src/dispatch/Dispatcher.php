<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\ActionAddUser;
use iutnc\touiteur\action\ActionConnexion;
use iutnc\touiteur\action\ActionDefault;
use iutnc\touiteur\action\ActionAfficherTouites;
use iutnc\touiteur\action\ActionAfficherUsers;
use iutnc\touiteur\action\ActionAfficherTags;

class Dispatcher{
    private string $action="";

    public function __construct() {
        if(isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    public function run(){
        switch ($this->action) {
            case "add-user":
                $action = new ActionAddUser();
                $affichage = $action->execute();
                break;

            case "connexion":
                $action = new ActionConnexion();
                $affichage = $action->execute();
                break;
            
            //affiche tous les touites
            case "display-touites":
                $action = new ActionAfficherTouites();
                $affichage = $action->execute();
                break;

            //affiche tous les utilisateurs
            case "display-users":
                $action = new ActionAfficherUsers();
                $affichage = $action->execute();
                break;

            //affiche tous les tags
            case "display-tags":
                $action = new ActionAfficherTags();
                $affichage = $action->execute();
                break;

            default:
                $action = new ActionDefault();
                $affichage = $action->execute();                  
        }
        $this->renderPage($affichage);
    }

    private function renderPage(string $affichage) : void {
        $content = <<<EOT
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <title>TOUITEUR</title>
                <meta charset="utf-8">
            </head>
            <body>
                <h1>TOUITEUR</h1>
                {$affichage}
                <nav>
                    <ul>
                        <li><a href="main.php">Accueil</a></li>
                        <li><a href="main.php?action=add-user">Inscription</a></li>
                        <li><a href="main.php?action=connexion">Connexion</a></li>
                        <li><a href="main.php?action=display-touites">Afficher tous les touites</a></li>
                        <li><a href="main.php?action=display-tags">Afficher tous les tags</a></li>
                    </ul>
                </nav>
            </body>
        </html>
        EOT;
        echo $content;
    }
}