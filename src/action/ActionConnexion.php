<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\exception\AuthException;

class ActionConnexion extends Action
{
    public function execute(): string
    {
        $aff = "";
        if ($this->http_method == "GET") {
            $aff .= '<form id="connexion" method="POST" action="?action=connexion">
                <input type="text" name="username" placeholder="<username>" required>
                <input type="password" name="password" placeholder="<password>" required>
                <button type="submit">Connexion</button>
                </form>';
        } 
        else if ($this->http_method == "POST") {
            $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);
            try {
                Auth::authenticate($username, $password);
                $aff .= "Connexion effectuée";
            } 
            catch (AuthException $e) {
                $aff .= "Connexion Impossible";
            }
        }
        return $aff;
    }
}