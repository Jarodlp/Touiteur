<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\ActionAddUser;
use iutnc\touiteur\action\ActionConnexion;
use iutnc\touiteur\action\ActionDefault;
use iutnc\touiteur\action\ActionAfficherTouite;
use iutnc\touiteur\action\ActionAfficherUser;
use iutnc\touiteur\action\ActionAfficherTag;

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
            case "display-touite":
                $action = new ActionAfficherTouite();
                $affichage = $action->execute();
                break;

            //affiche tous les utilisateurs
            case "display-user":
                $action = new ActionAfficherUser();
                $affichage = $action->execute();
                break;

            //affiche tous les tags
            case "display-tag":
                $action = new ActionAfficherTag();
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
                        <li><a href="main.php?action=display-touite">Afficher tous les touites</a></li>
                        <li><a href="main.php?action=display-user">Afficher tous les utilisateurs</a></li>
                        <li><a href="main.php?action=display-tag">Afficher tous les tags</a></li>
                    </ul>
                </nav>
            </body>
        </html>
        EOT;
        echo $content;
    }
}