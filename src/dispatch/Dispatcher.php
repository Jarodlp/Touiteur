<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\ActionAddUser;
use iutnc\touiteur\action\ActionConnexion;
use iutnc\touiteur\action\ActionDeconnection;
use iutnc\touiteur\action\ActionDefault;

use iutnc\touiteur\action\ActionFollow;
use iutnc\touiteur\action\ActionFollowTag;

use iutnc\touiteur\action\ActionPublierTouite;
use iutnc\touiteur\action\ActionSupprimerTouite;

use iutnc\touiteur\action\ActionAfficherTouite;
use iutnc\touiteur\action\ActionAfficherTag;
use iutnc\touiteur\action\ActionAfficherUser;
use iutnc\touiteur\action\ActionAfficherMur;
use iutnc\touiteur\action\ActionAfficherToutTouites;
use iutnc\touiteur\auth\Auth;

class Dispatcher{
    private string $action="";

    public function __construct() {
        if(isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    public function run() : void {
        switch ($this->action) {
            case "add-user":
                $action = new ActionAddUser();
                $affichage = $action->execute();
                break;

            case "connexion":
                $action = new ActionConnexion();
                $affichage = $action->execute();
                break;

            case "deconnection":
                $action = new ActionDeconnection();
                $affichage = $action->execute();
                break;
            
            //affiche tous les touites
            case "display-touite":
                $action = new ActionAfficherTouite();
                $affichage = $action->execute();
                break;

            case "display-user":
                $action = new ActionAfficherUser();
                $affichage = $action->execute();
                break;

            case "display-tag":
                $action = new ActionAfficherTag();
                $affichage = $action->execute();
                break;

            case "display-all-touites":
                $action = new ActionAfficherToutTouites();
                $affichage = $action->execute();
                break;

            case "display-mur":
                $action = new ActionAfficherMur();
                $affichage = $action->execute();
                break;

            case "publier-touite":
                $action = new ActionPublierTouite();
                $affichage = $action->execute();
                break;

            case "follow":
                $action = new ActionFollow();
                $affichage = $action->execute();
                break;

            case "followTag":
                $action = new ActionFollowTag();
                $affichage = $action->execute();
                break;

            case "supprimer-touite":
                $action = new ActionSupprimerTouite();
                $affichage = $action->execute();
                break;

            default:
                $action = new ActionDefault();
                $affichage = $action->execute();                  
        }
        $this->renderPage($affichage, self::menu());
    }

    private function renderPage(string $affichage, string $menu) : void {
        $content = <<<EOT
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <title>TOUITEUR</title>
                <meta charset="utf-8">
                <link href="style.css" rel="stylesheet">
            </head>
            <body>
                <header>    
                    <h1>TOUITEUR</h1>
                </header>
                <main>
                    {$affichage}
                </main>
                <nav>
                    <ul>
                        {$menu}
                    </ul>
                </nav>
            </body>
        </html>
        EOT;
        echo $content;
    }

    private function menu() : string {
        $aff="";
        $aff.='<li><a href="main.php">Accueil</a></li><br>                
                <li><a href="main.php?action=display-all-touites&param=none&page=1">Afficher tous les touites</a></li><br>';
        //si l'utilisateur n'est pas connecté on enlève certaines possibilités
        if (isset($_SESSION["user"])) {
            $user = unserialize($_SESSION["user"]);
            //si l'utilisateur est un admin, il peut accéder au pack office
            if (Auth::checkUserEstAdmin($user->username)){
                $aff .= '<li><a href="mainBO.php?">Accéder au back office</a></li><br>';
            }
            $aff .= '<li><a href="main.php?action=display-mur&param=perso&page=1">Afficher mon mur</a></li><br>
                <li><a href="main.php?action=publier-touite">Publier un touite</a></li><br>
                <li><a href="main.php?action=display-user&username='.$user->username.'&page=1">Afficher mon profil</a></li><br>
                <li><a href="main.php?action=deconnection">Se déconnecter</a></li><br>';
        } 
        else {
            $aff.='<li><a href="main.php?action=add-user">Inscription</a></li><br>
                    <li><a href="main.php?action=connexion">Connexion</a></li><br>';
        }                             
        return $aff;
    }
}