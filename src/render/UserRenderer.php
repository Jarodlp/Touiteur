<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\user\User;

class UserRenderer implements Renderer{
    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    //affichage du user
    public function render(int $selector) : string {
        if ($selector == self::COMPACT) {
            return $this->renderCompact();
        }
        else if ($selector == self::LONG) {
            return $this->renderLong();
        }
    }

    //affichage simple 
    public function renderCompact() : string {
        return "<a href='main.php?action=display-user&username={$this->user->username}&page=1'>{$this->user->username}</a><br>";
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        $aff="";
        $aff.="{$this->user->firstName} {$this->user->lastName}<br><br>";
        if (isset($_SESSION["user"])) {
            //on affiche le score moyen de ses touites et les personnes qui le suivent
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
                if (count($followers) == 0) {
                    $aff.="Personne n'est abonné à vous :(";
                }
                else if (count($followers) == 1) {
                    $aff.="Vous avez 1 abonné :<br>";
                    $userRenderer = new UserRenderer($followers[0]);
                    $aff.=$userRenderer->render(1);
                }
                else {
                    $aff.="Vous avez ".count($followers)." abonnés :<br>";
                    foreach ($followers as $key => $value) {
                        $userRenderer = new UserRenderer($value);
                        $aff.=$userRenderer->render(1);
                    }
                }
                //on affiche les utilisateur que l'utilisateur follow
                $follows = $user->getFollow();
                if (count($follows) == 0) {
                    $aff.="Vous n'êtes abonné à personne<br>";
                }
                else if (count($follows) == 1) {
                    $aff.="Vous êtes abonné à 1 personne :<br>";
                    $userRenderer = new UserRenderer($follows[0]);
                    $aff.=$userRenderer->render(1);
                }
                else {
                    $aff.="Vous êtes abonné à ".count($follows)." personnes :<br>";
                    foreach ($follows as $key => $value) {
                        $userRenderer = new UserRenderer($value);
                        $aff.=$userRenderer->render(1);
                    }
                }
            }
             //lien pour follow l'utilisateur si ce n'est pas nous
            else {
                $aff.="<a href='main.php?action=follow&username={$this->user->username}'>Follow {$this->user->username}</a><br>";
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