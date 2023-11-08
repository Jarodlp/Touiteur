<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\db\ConnectionFactory;

class ActionAddUser extends Action {
    public function execute () : string {
        $aff = "";
        if ($this->http_method == "GET") {
            $aff.='<form id="add-user" method="POST" action="?action=add-user">
                <input type="text" name="username" placeholder="<username>">
                <input type="password" name="password" placeholder="<password>">
                <input type="text" name="prenom" placeholder="<prenom>">
                <input type="text" name="nom" placeholder="<nom>">
                <input type="email" name="email" placeholder="<email>">
                <button type="submit">Connexion</button>
                </form>';
            $aff.="Veuillez utiliser un mot de passe de minimum 10 caractères, avec au moins un caractère spécial, une minuscule, une majuscule et un entier";
        }
        else if ($this->http_method == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $prenom = $_POST["prenom"];
            $nom = $_POST["nom"];
            $email = $_POST["email"];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $aff.="Email invalide !<br>";
            }
            else {
                if (Auth::register($password, $username)) {
                    $connexion = ConnectionFactory::makeConnection();
                    $statement = $connexion->prepare('UPDATE user SET firstName = ?, lastName = ?,
                    email = ? where username = ? ');
                    $statement->bindParam(1, $prenom);
                    $statement->bindParam(2, $nom);
                    $statement->bindParam(3, $email);
                    $statement->bindParam(4, $username);
                    $statement->execute();
                    $aff.="Utilisateur ajouté dans la base de donnée";
                }
                else {
                    $aff.="Mot de passe pas assez fort et/ou nom d'utilisateur déjà pris";
                }
            }
       }
       return $aff;
    }
}