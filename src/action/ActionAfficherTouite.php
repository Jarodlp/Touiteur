<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\touite\Tag;
use \iutnc\touiteur\render\TagRenderer;
use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\UserRenderer;

use \iutnc\touiteur\db\ConnectionFactory;

class ActionAfficherTouite extends Action {
    public function execute () :string {
        $affichage = "";

        switch ($_GET["param"]) {
            //on affiche tout les touites
            case "none":
                 //on récupère tous les touites de la BD
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM touite ORDER BY dateTouite DESC";
                $result = $connexion->query($query);
                while ($data = $result->fetch()) {
                    $texte = $data["text"];
                    $username = $data["username"];
                    $touite = new Touite($data["idTouite"], $texte, $username);
                    $touiteRender = new TouiteRenderer($touite);
                    $affichage.=$touiteRender->render(1);
                }
                break;

            //on affiche un seul touite
            case "one":
                $connexion = ConnectionFactory::makeConnection();
                $erreur="";
                //on récupère le touite dans la BD grâce à son id et on l'affiche avec un lien par tags et le lien de l'utilisateur
                $query = "SELECT * FROM touite WHERE idTouite = ?";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1, $_GET["id"]);
                $statement->execute();
                $data = $statement->fetch();

                $texte = $data["text"];
                $username = $data["username"];

                $tags = [];
                //récupère les tags du texte du touite
                $query3 = "SELECT title FROM tag 
                INNER JOIN touitetag ON tag.idTag = touitetag.idTag
                INNER JOIN touite ON touite.idTouite = touitetag.idTouite
                WHERE touite.idTouite = ?";
                $statment3 = $connexion->prepare($query3);
                $statment3->bindParam(1, $data["idTouite"]);
                $statment3->execute();
                while ($data2 = $statment3->fetch()) {
                    $tags[] = $data2["title"];
                }

                $touite = new Touite($data["idTouite"], $texte, $username, $tags);
                $touiteRender = new TouiteRenderer($touite);
                $affichage.=$touiteRender->render(2);
                $affichage.="<br>".$erreur;
                break;

            //on affiche les touites d'un tag
            case "tag":
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM tag WHERE tag.title = ?";
                $statment = $connexion->prepare($query);
                $statment->bindParam(1, $_GET["title"]);
                $statment->execute();
                $data = $statment->fetch();
                $tag = new Tag($data["title"], $data["descriptionTag"]);
                $tagRender = new TagRenderer($tag);
                $affichage.=$tagRender->render(2);

                //on affiche les touites du tag
                $statment=$connexion->prepare("SELECT * FROM touite
                INNER JOIN touiteTag ON touiteTag.idTouite = touite.idTouite
                INNER JOIN tag ON tag.idTag = touiteTag.idTag 
                WHERE tag.title = ?");
                $tagTitle = $tag->__get("title");
                $statment->bindParam(1, $tagTitle);
                $statment->execute();
                while($donnees=$statment->fetch()){
                    $touite=new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                    $touiteRenderer=new TouiteRenderer($touite);
                    $affichage.=$touiteRenderer->render(1);
                }
                break;
            
            //on affiche les touites d'un utilisateur
            case "user":
                $db = ConnectionFactory::makeConnection();

                $statment=$db->prepare("SELECT * FROM user WHERE user.username=:u_username");
                $statment->bindParam(':u_username',$_GET["username"],\PDO::PARAM_STR);
                $statment->execute();
                //on a besoin que d'une seule ligne car on traîte un seul utilisateur
                $donnees=$statment->fetch();
                $user = new User($donnees['username'],$donnees['password'],$donnees['email'],$donnees['firstName'],$donnees['lastName']);
                $userRenderer = new UserRenderer($user);
                $affichage.=$userRenderer->render(2);

                //on récupère les tweets de l'utilisateur courant affin de les afficher sur son mur
                $statment=$db->prepare("SELECT * FROM touite WHERE touite.username = ?");
                $username=$user->username;
                $statment->bindParam(1,$username);
                $statment->execute();
                while($donnees=$statment->fetch()){
                    $touite=new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                    $touiteRenderer=new TouiteRenderer($touite);
                    $affichage.=$touiteRenderer->render(1);
                }
                break;
            
            default:   
                $affichage.="Erreur de redirection";     
        }
        return $affichage;
    }
}