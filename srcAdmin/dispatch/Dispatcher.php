<?php

namespace iutnc\touiteurBO\dispatch;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteurBO\action\ActionAfficherInfluenceurs;
use iutnc\touiteurBO\action\ActionDefault;

class Dispatcher{
    private string $action="";

    public function __construct() {
        if(isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    public function run()
    {
        if (isset($_SESSION)) {
            $user = unserialize($_SESSION["user"]);
            if (Auth::checkUserEstAdmin($user->username)) {

                switch ($this->action) {
                    case "afficher-influenceur":
                        $action = new ActionAfficherInfluenceurs();
                        $affichage = $action->execute();
                        break;

                    case "connexion":
                        $action = new ActionConnexion();
                        $affichage = $action->execute();
                        break;

                    default:
                        $action = new ActionDefault();
                        $affichage = $action->execute();
                }
                $this->renderPage($affichage, self::menu());

            } else {
                $this->renderPage("vous n'êtes pas admin, vous n'avez pas accès au back office <br><br>",
                    '<li><a href="main.php">Retourner sur Touiteur</a></li><br>');
            }
        } else {
            $this->renderPage("vous n'êtes pas connecté et vous essayer d'accéder au back office de façon
            intrusive",'<li><a href="main.php">Retourner sur Touiteur</a></li><br>');
        }
    }

    private function renderPage(string $affichage, string $menu) : void {
        $content = <<<EOT
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <title>TOUITEUR BACK OFFICE</title>
                <meta charset="utf-8">
                <link href="style.css" rel="stylesheet">
            </head>
            <body>
                <h1>TOUITEUR BACK OFFICE</h1>
                {$affichage}
                {$menu}
            </body>
        </html>
        EOT;
        echo $content;
    }

    private function menu() : string {
        $aff ='<nav>
                <ul>         
                <li><a href="mainBO.php?action=afficher-influenceur">Afficher la liste des influenceurs</a></li><br>
                <li><a href="mainBO.php">Retourner sur l\'accueil du back office de Touiteur</a></li><br>
                <li><a href="main.php">Retourner sur Touiteur</a></li><br>';
        $aff.='</ul>
            </nav>';
        return $aff;
    }
}