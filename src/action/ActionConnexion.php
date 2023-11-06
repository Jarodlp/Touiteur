<?php

namespace iutnc\touiteur\action;

class ActionConnexion extends Action {
    public function execute() : string {
        $aff = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $aff.='<form id="add-user" method="POST" action="?action=connexion">
                <input type="email" name="email" value="email@email.com" placeholder="<email>">
                <input type="text" name="password" value="password" placeholder="<password>">
                <button type="submit">Connexion</button>
                </form>';
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $aff.="Email invalide !<br>";
            }
            else {
                filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
                try {
                    Auth::authenticate($email, $password);
                    $aff.="Connexion effectuée<br>";
                    //afficher liste des playlists appartenant à l'utilisateur
                    $aff.="Liste des playlists :<br>";
                    $hash = password_hash($password, PASSWORD_BCRYPT, ["cost"=>12]);
                    $user = new User($email, $hash);
                    $tracklists = $user->getTrackList();
                    for ($i = 0; $i < count($tracklists); $i++) {
                        $aff.="<a href='index.php?action=display-playlist&id=".$tracklists[$i]->id."'>".$tracklists[$i]->nom."</a><br>";
                    }
                }
                catch (AuthException $e) {
                    $aff.="Connexion Impossible";
                }
            }
        }
        return $aff;
    }
}