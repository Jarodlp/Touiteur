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
                    $touite = new Touite($data["idTouite"], $data["text"], $data["username"]);
                    $touiteRender = new TouiteRenderer($touite);
                    $affichage.=$touiteRender->render(1);
                }
                break;

            //on affiche un seul touite
            case "one":
                //on récupère le touite dans la BD grâce à son id et on l'affiche avec un lien par tags et le lien de l'utilisateur
                $idTouite = $_GET["id"];
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM touite WHERE idTouite = ?";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1, $idTouite);
                $statement->execute();
                $donnees = $statement->fetch();

                $texte = $donnees["text"];
                $username = $donnees["username"];

                //récupère les tags du texte du touite
                $tags = [];
                $query = "SELECT title FROM tag 
                INNER JOIN touitetag ON tag.idTag = touitetag.idTag
                INNER JOIN touite ON touite.idTouite = touitetag.idTouite
                WHERE touite.idTouite = ?";
                $statment = $connexion->prepare($query);
                $statment->bindParam(1, $idTouite);
                $statment->execute();
                while ($donnee = $statment->fetch()) {
                    $tags[] = $donnee["title"];
                }
                $touite = new Touite($idTouite, $texte, $username, $tags);
                $touiteRender = new TouiteRenderer($touite);
                $affichage.=$touiteRender->render(2);
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
                $affichage.="<br>";
                
                //on affiche les touites du tag
                $statment=$connexion->prepare("SELECT * FROM touite
                INNER JOIN touiteTag ON touiteTag.idTouite = touite.idTouite
                INNER JOIN tag ON tag.idTag = touiteTag.idTag 
                WHERE tag.title = ?");
                $tagTitle = $tag->title;
                $statment->bindParam(1, $tagTitle);
                $statment->execute();
                while($donnees = $statment->fetch()){
                    $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                    $touiteRenderer = new TouiteRenderer($touite);
                    $affichage.=$touiteRenderer->render(1);
                }
                break;
            
            //on affiche les touites d'un utilisateur
            case "user":
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM user WHERE user.username=:u_username";
                $statment = $connexion->prepare($query);
                $statment->bindParam(':u_username', $_GET["username"], \PDO::PARAM_STR);
                $statment->execute();
                //on a besoin que d'une seule ligne car on traîte un seul utilisateur
                $donnees = $statment->fetch();
                $user = new User($donnees['username'],$donnees['password'],$donnees['email'],$donnees['firstName'],$donnees['lastName']);
                $userRenderer = new UserRenderer($user);
                $affichage.=$userRenderer->render(2);

                //on récupère les touites de l'utilisateur courant affin de les afficher sur son mur
                $statment = $connexion->prepare("SELECT * FROM touite WHERE touite.username = ?");
                $username = $user->username;
                $statment->bindParam(1,$username);
                $statment->execute();
                while($donnees = $statment->fetch()){
                    $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                    $touiteRenderer = new TouiteRenderer($touite);
                    $affichage.=$touiteRenderer->render(1);
                }
                break;
            
            //on affiche le mur de l'utilisateur avec les touites qui l'intéressent
            case "perso":
                //on teste si l'utilisateur est connecté
                if (isset($_SESSION["user"])) {
                    $user = unserialize($_SESSION["user"]);
                    $username = $user->username;
                    $connexion = ConnectionFactory::makeConnection();
                    $query = "SELECT touite.idTouite, touite.text, touite.username, touite.dateTouite FROM touite
                    INNER JOIN touitetag ON touitetag.idTouite = touite.idTouite
                    INNER JOIN tagfollowed ON tagfollowed.idTag = touitetag.idTag
                    INNER JOIN user ON user.username = tagfollowed.username
                    WHERE user.username = ?
  		            UNION
                    SELECT touite.idTouite, touite.text, touite.username, touite.dateTouite FROM touite
                    INNER JOIN userfollowed ON userfollowed.usernamefollowed = touite.username
                    INNER JOIN user ON user.username = userfollowed.username
                    WHERE user.username = ?
                    ORDER BY dateTouite DESC";
                    $statment = $connexion->prepare($query);
                    $statment->bindParam(1, $username);
                    $statment->bindParam(2, $username);
                    $statment->execute();
                    while($donnees = $statment->fetch()){
                        $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                        $touiteRenderer = new TouiteRenderer($touite);
                        $affichage.=$touiteRenderer->render(1);
                    }
                    $affichage.="<br><br>";
                    $affichage.='<form id="form1" method="GET" action="main.php?action=display-touite&param=tag">'.
                                '<input type="text" name="title">'.
                                '<button type="submit">Rechercher un Tag</button>'.
                                '</form>';
                }
                else {
                    $affichage.="Vous n'êtes pas connecté, vous ne pouvez pas afficher votre mur";
                }
                break;
            
                default:   
                    $affichage.="Erreur de redirection";     
        }
        return $affichage;
    }
}