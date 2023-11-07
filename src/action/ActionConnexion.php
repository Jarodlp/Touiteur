<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\exception\AuthException;
use iutnc\touiteur\user\User;

class ActionConnexion extends Action
{
    public function execute(): string
    {
        $aff = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $aff .= '<form id="connexion" method="POST" action="?action=connexion">
                <input type="text" name="username" placeholder="<username>" required>
                <input type="password" name="password" placeholder="<password>" required>
                <button type="submit">Connexion</button>
                </form>';
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];

            filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
            try {
                Auth::authenticate($username, $password);
                $aff .= "Connexion effectuée";
            } catch (AuthException $e) {
                $aff .= "Connexion Impossible";
            }
        }
        return $aff;
    }
}