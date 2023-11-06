<?php

namespace iutnc\touiteur\action;

class ActionAddUser extends Action {
    public function execute () : string {
        $aff = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $aff.='<form id="add-user" method="POST" action="?action=add-user">
                <input type="email" name="email" value="email@email.com" placeholder="<email>">
                <input type="text" name="password" value="password" placeholder="<password>">
                <button type="submit">Connexion</button>
                </form>';
            $aff.="Utilisez un mot de passe de minimum 10 caractères, avec au moins un caractère spécial, une minuscule, une majuscule et un entier";
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $aff.="Email invalide !<br>";
            }
            else {
                if (Auth::register($password, $email)) {
                    $aff.="Utilisateur ajouté dans la base de donnée";
                }
                else {
                    $aff.="Mot de passe pas assez fort OU Adresse mail déjà présente";
                }
            }
       }
       return $aff;
    }
}