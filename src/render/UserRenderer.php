<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\user\User;

class UserRenderer implements Renderer{
    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    //affichage du user
    public function render(int $selector): string {
        if ($selector == self::COMPACT) {
            return $this->renderCompact();
        }
        else if ($selector == self::LONG) {
            return $this->renderLong();
        }
    }

    //affichage simple 
    public function renderCompact() : string {
        return "Username de l'utilisateur : {$this->user->username}<br>";
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        $aff="";
        $aff.="Profil de :<br>";
        $aff.="Prénom : {$this->user->firstName}<br>Nom : {$this->user->lastName}<br><br>";
        //on affiche le score moyen de ses touites et les personnes qui le suivent
        if (isset($_SESSION["user"])) {
            $user = unserialize($_SESSION["user"]);
            //si la page de l'user afficher et le même que l'utilisateur connecté
            if($user->username == $this->user->username) {
                $score = $user->getScoreTouites();
                //si le score est null, c'est qu'il n'y a pas de note sur les touites ou pas de touite
                if ($score == NULL) {
                    $aff.="Vous n'avez pas de touites ou bien vos touites n'ont pas de notes<br><br>";
                }
                else {
                    $aff.="Score moyen de vos touites : ".$score."<br><br>";
                }
                //on affiche les followers abbonés à l'utilisateur
                $followers = $user->getFollower();
                $aff.="Utilisateurs qui vous suivent :<br><br>";
                foreach ($followers as $key => $value) {
                    $user = $value;
                    $userRenderer = new UserRenderer($user);
                    $aff.=$userRenderer->render(1);
                }
                //on affiche le nombre d'utilisateur abbonés à l'utilisateur
                $aff.=$user->getNombreFollower()." utilisateurs sont abbonés à vous<br><br>";
                //on affiche le nombre d'utilisateurs auquel on n'est abboné
                $aff.="Vous êtes abboné à ".$user->getNombreFollow()." utilisateurs <br><br>";
            }
        }
        return $aff;
    }

    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}