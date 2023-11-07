<?php

namespace iutnc\touiteur\auth;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\exception\AuthException;

class Auth {
    //connexion de l'utilisateur
    public static function authenticate(string $email, string $password) : void {
        //on teste d'abord si l'adresse mail est présente
        $connexion = ConnectionFactory::makeConnection();
        $query = 'SELECT COUNT(*) FROM user AS u WHERE u.email= ?';
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $email);
        $statment->execute();
        $result = $statment->fetch();
        $adressPresente = ($result[0] == 1);
        if ($adressPresente) {
            //on cherche le mdp hashé correspondant à l'adresse mail en supposant qu'elle soit unique
            $connexion = ConnectionFactory::makeConnection();
            $query = 'SELECT passwd FROM user AS u WHERE u.email= ?';
            $statment = $connexion->prepare($query);
            $statment->bindParam(1, $email);
            $statment->execute();
            $result = $statment->fetch();
            $hash = $result['passwd'];
            if (!password_verify($password, $hash)) {
                throw new AuthException();
            }
            else {
                //on enregistre l'utilisateur dans la session
                self::loadProfile($email);
            }
        }
        else {
            throw new AuthException();
        }
    }

    //inscription d'un utilisateur
    public static function register(string $password, string $email): bool {
        //test si l'adresse mail est déjà présente
        $connexion = ConnectionFactory::makeConnection();
        $query = 'SELECT COUNT(*) FROM user AS u WHERE u.email= ?';
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $email);
        $statment->execute();
        $result = $statment->fetch();
        $adressPresente = ($result[0] == 1);
        if (self::checkPasswordStrenght($password, 10) && !$adressPresente) {
            $hashPassword = password_hash($password, PASSWORD_BCRYPT, ['cost'=> 12]);
            $query = 'INSERT INTO user (email, passwd, role) VALUES (:email, :password, :role)';
            $statment = $connexion->prepare($query);
            $statment->bindParam(':email', $email);
            $statment->bindParam(':password', $hashPassword);
            $statment->bindParam(':role', $role);
            $role = 1;
            $insertion = $statment->execute();
            return true;
        }
        else {
            return false;
        }
    }

    //test la force du mdp
    public static function checkPasswordStrenght(string $password, int $minimumLength): bool {
        $length = (strlen($password) > $minimumLength); // longueur minimale
        $digit = preg_match("#[\d]#", $password); // au moins un digit
        $special = preg_match("#[\W]#", $password); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $password); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $password); // au moins une majuscule
        if ($length && $digit && $special && $lower && $upper){return true;}
        else{return false;}
    }

    //charge l'utilisateur dans la session
    public static function loadProfile(string $email): void {
        $connexion = ConnectionFactory::makeConnection();
        $query = 'SELECT id, role FROM user AS u WHERE u.email= ?';
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $email);
        $statment->execute();
        $result = $statment->fetch();
        $_SESSION['user'] = ["id" => $result["id"], "role" => $result["role"]];
    }

    //test si l'utilisateur peut accéder à une playlist
    public static function checkPlaylist(int $idPlaylist): bool {
        $connexion = ConnectionFactory::makeConnection();
        $query = 'SELECT u2p.id_pl FROM user AS u INNER JOIN user2playlist AS u2p ON u2p.id_user = u.id WHERE u.id= ?';
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $_SESSION["user"]["id"]);
        $statment->execute();
        $playlistAppartient = false;
        while ($data = $statment->fetch()) {
            if ($data["id_pl"] === $idPlaylist) {
                $playlistAppartient = true;
            }
        }
        if ($_SESSION["user"]["role"] === 100 || $playlistAppartient) {
            return true;
        }
        else {
            return false;
        }
    }
}