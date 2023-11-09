<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\render\TouiteRenderer;

class User
{
    protected string $username;
    protected string $password;
    protected string $email;
    protected string $firstName;
    protected string $lastName;

    public function __construct($username, $password, $email, $firstName, $lastName)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    //getter magique
    public function __get(string $attr): mixed
    {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        } else {
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }

    //l'utilisateur publie un touite
    public function publieTouite(string $texte, string $cheminImage, array $tags = []): Touite
    {
        //On regarde les tags présents dans le texte et on les ajoute à la BD si ils n'existent pas
        $auteur = $this->username;
        $connexion = ConnectionFactory::makeConnection();
        // on insère le touite dans la bd et on récupère son id ensuite
        $statement = $connexion->prepare('insert into touite(username,text,dateTouite) values (?,?,sysdate())');
        $statement->bindParam(1, $auteur);
        $statement->bindParam(2, $texte);
        $statement->execute();

        $statement = $connexion->prepare('select max(idTouite) from touite where username = ? and text = ?');
        $statement->bindParam(1, $auteur);
        $statement->bindParam(2, $texte);
        $statement->execute();
        $result = $statement->fetch();
        $idTouite = $result[0];

        // boucles pour les tags
        foreach ($tags as $tag) {
            // on recherche si le tag existe déjà dans la BD
            $statement = $connexion->prepare('SELECT COUNT(*) FROM tag WHERE title = ?');
            $statement->bindParam(1, $tag);
            $statement->execute();
            $result = $statement->fetch();
            // S'il existe pas, on le crée
            if ($result[0] == 0) {
                $statement = $connexion->prepare('insert into tag(title, descriptionTag) values (?, ?)');
                $statement->bindParam(1, $tag);
                $statement->bindParam(2, $tag);
                $statement->execute();
            }
            // On récupère l'id du tag
            $statement = $connexion->prepare('select idTag from tag where title = ?');
            $statement->bindParam(1, $tag);
            $statement->execute();
            $result = $statement->fetch();
            $idTag = $result[0];
            // Et ensuite on insère la liaison entre le touite et son/ses tags dans la table touiteTag
            $statement = $connexion->prepare('insert into touiteTag values (?,?)');
            $statement->bindParam(1, $idTouite);
            $statement->bindParam(2, $idTag);
            $statement->execute();
        }

        //Instruction pour l'image
        if ($cheminImage !== "pasDimage") {
            // On insère l'image dans la bd si elle existe pas
            $statement = $connexion->prepare('select count(*) from Image where fileName = ?');
            $statement->bindParam(1, $cheminImage);
            $statement->execute();
            $result = $statement->fetch();
            if ($result[0] == 0) {
                $statement = $connexion->prepare('insert into image(fileName) values (?)');
                $statement->bindParam(1, $cheminImage);
                $statement->execute();
            }

            // Et on récupère son ID
            $statement = $connexion->prepare('select idImage from Image where fileName = ?');
            $statement->bindParam(1, $cheminImage);
            $statement->execute();
            $result = $statement->fetch();
            $idImage = $result[0];

            // Et on insère la liaison entre le touite et son image dans la bd
            $statement = $connexion->prepare('insert into touiteImage values (?,?)');
            $statement->bindParam(1, $idTouite);
            $statement->bindParam(2, $idImage);
            $statement->execute();
        }

        return new Touite($idTouite, $texte, $auteur, $tags, 0, $cheminImage);
    }

    public function getScoreTouites(): mixed
    {
        $user = unserialize($_SESSION["user"]);
        $username = $user->username;
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT AVG(touitenote.note) FROM touite
        INNER JOIN touitenote ON touitenote.idTouite = touite.idTouite 
        WHERE touite.username = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $username);
        $statment->execute();
        $donnees = $statment->fetch();
        $score = $donnees[0];
        return $score;
    }

    public static function getMur(bool $erreur = false): string
    {
        $affichage = "";
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
            $affichage .= "Touites :<br><br>";
            while ($donnees = $statment->fetch()) {
                $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                $touiteRenderer = new TouiteRenderer($touite);
                $affichage .= $touiteRenderer->render(1);
            }
            $affichage .= "<br><br>";
            if ($erreur) {
                $affichage .= "Tag inexistant, veuillez entrer  un nom valide<br>Pensez à enlever le # si vous en avez mis un.";
            }
            $action = "action=display-touite";
            $affichage .= "<form id='form1' method='GET' action='main.php'>" .
                "<input type='text' name='title'>" .
                "<input type='hidden' name='action' value='display-touite'>" .
                "<input type='hidden' name='param' value='tag'>" .
                "<button type='submit'>Rechercher un Tag</button>" .
                "</form>";
        } else {
            $affichage .= "Vous n'êtes pas connecté, vous ne pouvez pas afficher votre mur";
        }
        return $affichage;
    }
}