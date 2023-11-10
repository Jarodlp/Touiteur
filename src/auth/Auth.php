<?php

namespace iutnc\touiteur\auth;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\exception\AuthException;
use iutnc\touiteur\user\User;

class Auth {
    //connexion de l'utilisateur
    public static function authenticate(string $username, string $password) : void {
        //on teste d'abord si le nom d'utilisateur est présent dans la base de données
        $connexion = ConnectionFactory::makeConnection();
        $statement = $connexion->prepare('SELECT COUNT(*) FROM user WHERE username = ?');
        $statement->bindParam(1, $username);
        $statement->execute();
        $result = $statement->fetch();
        if ($result[0] == 1) {
            //on cherche le mdp hashé correspondant au nom d'utilisateur qui est unique à chaque utilisateur
            $connexion = ConnectionFactory::makeConnection();
            $statement = $connexion->prepare('SELECT password FROM user WHERE username = ?');
            $statement->bindParam(1, $username);
            $statement->execute();
            $result = $statement->fetch();
            if (!password_verify($password, $result['password'])) {
                throw new AuthException();
            }
            else {
                //on enregistre l'utilisateur dans la session
                self::loadProfile($username);
            }
        }
        else {
            throw new AuthException();
        }
    }

    //inscription d'un utilisateur
    public static function register(string $password, string $username): bool {
        //test si le nom d'utilisateur est déjà présent
        $connexion = ConnectionFactory::makeConnection();
        $statement = $connexion->prepare('SELECT COUNT(*) FROM user WHERE username = ?');
        $statement->bindParam(1, $username);
        $statement->execute();
        $result = $statement->fetch();
        $usernamepris = ($result[0] == 1);
        if (self::checkPasswordStrenght($password, 10) && !$usernamepris) {
            $hashPassword = password_hash($password, PASSWORD_BCRYPT, ['cost'=> 12]);
            $query = 'INSERT INTO user (username, password) VALUES (?, ?)';
            $statement = $connexion->prepare($query);
            $statement->bindParam(1, $username);
            $statement->bindParam(2, $hashPassword);
            $statement->execute();
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

    public static function checkUserEstAdmin(string $username) : bool{
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT count(*) FROM admin WHERE username = ?";
        $statement = $connexion->prepare($query);
        $statement->bindParam(1, $username);
        $statement->execute();
        $result = $statement->fetch();
        return ($result[0] === 1);
    }

    //charge l'utilisateur dans la session
    public static function loadProfile(string $username): void {
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM user WHERE username = ?";
        $statement = $connexion->prepare($query);
        $statement->bindParam(1, $username);
        $statement->execute();
        $result = $statement->fetch();
        $_SESSION['user'] = serialize(new User($result['username'],$result['password'],$result['email']
        ,$result['firstName'],$result['lastName']));
    }


}