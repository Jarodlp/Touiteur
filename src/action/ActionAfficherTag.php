<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\render\UserRenderer;
use iutnc\touiteur\user\User;
use iutnc\touiteur\list\ListTouite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\render\ListTouiteRenderer;

class ActionAfficherTag extends Action {
    public function execute() : string {
        $affichage="";
        //on teste le cas où l'utilisateur cliquerait sur le bouton 'Rechercher un tag' sans avoir fourni de titre.
        //On lui réaffiche donc simplement son mur
        if(strlen($_GET["title"])==0){
            $affichage.=User::getMur();
        } else if(!Tag::tagExist($_GET["title"])){
                //maintenant on regarde si le user a entré un tag inexistant
                //auquel cas on réaffiche son mur avec un message d'erreur en plus
            $erreur=true;
            $affichage.=User::getMur($erreur);
        } else{
            //on récupère le tag grâce à son titre passer en paramètre
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT * FROM tag WHERE tag.title = ?";
            $statment = $connexion->prepare($query);
            $statment->bindParam(1, $_GET["title"]);
            $statment->execute();
            $data = $statment->fetch();
            $tag = new Tag($data["title"], $data["descriptionTag"]);
            $tagRender = new TagRenderer($tag);
            $affichage.=$tagRender->render(2);
            $affichage.="<br>";
    
            //on affiche les touites du tag
            $statment=$connexion->prepare("SELECT * FROM touite
            INNER JOIN touiteTag ON touiteTag.idTouite = touite.idTouite
            INNER JOIN tag ON tag.idTag = touiteTag.idTag 
            WHERE tag.title = ?
            ORDER BY touite.dateTouite DESC");
            $tagTitle = $tag->title;
            $statment->bindParam(1, $tagTitle);
            $statment->execute();
            $listTouite=new ListTouite();
            while($donnees = $statment->fetch()){
                $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                $listTouite->addTouite($touite);
            }
            $listTouiteRenderer=new ListTouiteRenderer($listTouite);
            $affichage.=$listTouiteRenderer->render(1);
        }
        return $affichage;
    }
}