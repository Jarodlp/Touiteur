<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\touite\Touite;
use \iutnc\touiteur\render\TouiteRenderer;
use \iutnc\touiteur\list\ListTouite;
use \iutnc\touiteur\render\ListTouiteRenderer;

class User {
    protected string $username;
    protected string $password;
    protected string $email;
    protected string $firstName;
    protected string $lastName;

    public function __construct($username, $password, $email, $firstName, $lastName) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    //getter magique
    public function __get(string $attr) : mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        } else {
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }

    public function getScoreTouites() : mixed {
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT AVG(touitenote.note) FROM touite
        INNER JOIN touitenote ON touitenote.idTouite = touite.idTouite 
        WHERE touite.username = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $this->username);
        $statment->execute();
        $donnees = $statment->fetch();
        $score = $donnees[0];
        return $score;
    }

    public function getFollower() : array {
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT user.username, user.password, user.email, user.firstName, user.lastName 
        FROM userfollowed 
        INNER JOIN user ON user.username = userfollowed.username
        WHERE userfollowed.usernamefollowed = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $this->username);
        $statment->execute();
        $users = [];
        while ($user = $statment->fetch()) {
            $users[] = new User($user["username"], $user["password"], $user["email"], $user["firstName"], $user["lastName"]);
        }
        return $users;
    }
    
    public function getFollow() : array {
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT user.username, user.password, user.email, user.firstName, user.lastName FROM userfollowed 
        INNER JOIN user ON user.username = userfollowed.usernameFollowed
        WHERE userfollowed.username = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $this->username);
        $statment->execute();
        $users = [];
        while ($user = $statment->fetch()) {
            $users[] = new User($user["username"], $user["password"], $user["email"], $user["firstName"], $user["lastName"]);
        }
        return $users;
    }

    public static function getMur(bool $erreur = false) : string {
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
            $listTouite=new ListTouite();
            while($donnees = $statment->fetch()){
                $touite = new Touite($donnees["idTouite"], $donnees["text"], $donnees["username"]);
                $listTouite->addTouite($touite);
            }
            $listTouiteRenderer = new ListTouiteRenderer($listTouite);
            $affichage.=$listTouiteRenderer->render(1);
            $affichage.="<br><br>";
            if ($erreur) {
                $affichage .= "Tag inexistant, veuillez entrer  un nom valide<br>Pensez à enlever le # si vous en avez mis un.";
            }
            $action = "action=display-tag";
            $affichage .= "<form id='form1' method='GET' action='main.php'>".
                "<input type='text' name='title'>" .
                "<input type='hidden' name='action' value='display-tag'>".
                "<button type='submit'>Rechercher un Tag</button>" .
                "</form>";
        } else {
            $affichage .= "Vous n'êtes pas connecté, vous ne pouvez pas afficher votre mur";
        }
        return $affichage;
    }
}