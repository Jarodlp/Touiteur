<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\touite\Tag;
use \iutnc\touiteur\render\TagRenderer;
use \iutnc\touiteur\user\User;
use \iutnc\touiteur\render\UserRenderer;
use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\list\ListTouite;
use \iutnc\touiteur\render\ListTouiteRenderer;

class ActionAfficherTouite extends Action {
    public function execute () :string {
        $affichage = "";
        switch ($_GET["param"]) {
            //on affiche tout les touites
            case "none":
                //on récupère tous les touites de la BD
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM touite";
                $result = $connexion->query($query);
                $listTouite = new ListTouite();
                while ($data = $result->fetch()) {
                    $touite = new Touite($data["idTouite"], $data["text"], $data["username"]);
                    $listTouite->addTouite($touite);
                }
                $listTouiteRenderer = new ListTouiteRenderer ($listTouite);
                $affichage.=$listTouiteRenderer->render(1);
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

                //on actualise la note dans le cas où l'utilisateur aurait like ou dislike
                if(isset($_GET['note'])){
                    $affichage.=Touite::note($_GET['note'],$idTouite);
                }

                //on récupère le score du touite
                $query = "SELECT SUM(note) FROM touitenote WHERE idTouite = ?";
                $statment = $connexion->prepare($query);
                $statment->bindParam(1, $idTouite);
                $statment->execute();
                $result = $statment->fetch();
                $note = $result[0];
                if($note == null) {
                    $note = 0;
                }

                //on récupère le chemin de l'image
                $query = "SELECT fileName FROM image
                INNER JOIN touiteimage ON touiteimage.idImage = image.idImage
                INNER JOIN touite ON touite.idTouite = touiteImage.idTouite
                WHERE touite.idTouite = ?";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1, $idTouite);
                $statement->execute();
                $result = $statement->fetch();
                //si il n'y a pas d'image on n'affiche rien
                if ($result !== NULL) {
                    $cheminImage = $result[0];
                }
                else {
                    $cheminImage = "";
                }

                $touite = new Touite($idTouite, $texte, $username, $tags, $note, $cheminImage);
                $touiteRender = new TouiteRenderer($touite);

                $affichage.=$touiteRender->render(2);
                break;

            //on affiche les touites d'un tag
            case "tag":
                //on teste le cas où l'utilisateur cliquerait sur le bouton 'Rechercher un tag' sans avoir fourni de titre.
                //On lui réaffiche donc simplement son mur
                if(strlen($_GET["title"])==0){
                    $affichage.=User::getMur();
                } else if(!Tag::tagExist($_GET["title"])){
                     //maintenant on regare si le user a entré un tag inexistant
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
                    WHERE tag.title = ?");
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
                break;
            
            //on affiche les touites d'un utilisateur
            case "user":
                //on récupère l'utilisateur dans la BD grâce au paramètre username dans le GET
                //si il y a l'attribut username dans GET c'est que l'utilisateur veut afficher un utilisateur 
                if (isset($_GET["username"])) {
                    $username = $_GET["username"];
                }
                //sinon  il veut s'afficher lui même en ayant cliqué sur le lien afficher profil
                else if (isset($_SESSION["user"])){
                    $user = unserialize($_SESSION["user"]);
                    $username = $user->username;
                }
                //sinon ça veut dire qu'il n'est pas connecté
                $connexion = ConnectionFactory::makeConnection();
                $query = "SELECT * FROM user WHERE user.username=:u_username";
                $statment = $connexion->prepare($query);
                $statment->bindParam(':u_username', $username, \PDO::PARAM_STR);
                $statment->execute();
                $donnees = $statment->fetch();
                $user = new User($donnees['username'],$donnees['password'],$donnees['email'],$donnees['firstName'],$donnees['lastName']);
                $userRenderer = new UserRenderer($user);
                $affichage.=$userRenderer->render(2);

                //on récupère les touites de l'utilisateur courant affin de les afficher sur son mur
                $statment = $connexion->prepare("SELECT * FROM touite WHERE touite.username = ?");
                $username = $user->username;
                $statment->bindParam(1,$username);
                $statment->execute();
                $listTouite = new ListTouite();
                while($donnees = $statment->fetch()){
                    $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                    $listTouite->addTouite($touite);
                }
                $listTouiteRenderer = new ListTouiteRenderer($listTouite);
                $affichage.=$listTouiteRenderer->render(1);
                break;
            
            //on affiche le mur de l'utilisateur avec les touites qui l'intéressent
            case "perso":
                $affichage.=User::getMur();
                break;

            default:   
                $affichage.="Erreur de redirection";     
        }
        return $affichage;
    }
}