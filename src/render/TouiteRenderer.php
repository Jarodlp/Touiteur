<?php

namespace iutnc\touiteur\render;
use \iutnc\touiteur\touite\Touite;

class TouiteRenderer implements Renderer{
    
    protected Touite $touite;

    public function __construct(Touite $touite) {
        $this->touite = $touite;
    }

    //affichage du touite
    public function render(int $selector): string {
        if ($selector == self::COMPACT) {
            return $this->renderCompact();
        }
        else if ($selector == self::LONG) {
            return $this->renderLong();
        } else {
            return "";
        }
    }

    //affichage simple 
    private function renderCompact() : string {
        $aff="";
        $aff.="<a href='main.php?action=display-touite&param=one&id=".$this->touite->id."'>".$this->touite->texte."</a><br><br>";
        return $aff;
    }

    //affichage complet avec toutes les infos
    private function renderLong() : string {

        $aff ="Titre : ".$this->touite->texte."<br>";
        $aff.="Note : ".$this->touite->note."<br>";
        // On affiche l'image du tweet qs'il en a une
        if (!is_null($this->touite->cheminImage)){
            $aff .= '<img src="'.$this->touite->cheminImage.'"/>';
        }
        //on affiche l'utilisateur en lien pour afficher les touites de l'utilisateur
        $aff.="<a href='main.php?action=display-touite&param=user&username=".$this->touite->username."'>Auteur : ".$this->touite->username."</a><br>";
        //on affiche le lien pour follow l'utilisateur
        $aff.="<a href='main.php?action=follow&username={$this->touite->username}'>Follow {$this->touite->username}</a><br>";
        //on affiche tout les tags en lien pour afficher les touites de ces tags
        foreach ($this->touite->tags as $key => $value) {
            $aff.="<a href='main.php?action=display-touite&param=tag&title=".$value."'>Tag : ".$value."</a><br>";
        }
        //si le touite appartient à l'utilisateur, on ajoute la fonction de suppression du touite
        if (isset($_SESSION['user'])) {
            $user = unserialize($_SESSION["user"]);
            if ($user->username == $this->touite->username) {
                $aff.="<a href='main.php?action=supprimer-touite&id={$this->touite->id}'>Supprimer mon touite</a><br>";
            }
            //sinon on affiche la fonction noter le touite
            else {
                $aff.="<form id='form1' method='GET' action='main.php'>".
                    "<button type='submit' name='note' value='like'>Like</button>".
                    "<button type='submit' name='note' value='dislike'>Dislike</button>".
                    "<input type='hidden' name='action' value='display-touite'>".
                    "<input type='hidden' name='param' value='one'>".
                    "<input type='hidden' name='id' value={$this->touite->id}>".
                    "</form>";
            }
        }
        return $aff;
    }
}

//$aff.="<a href='main.php?action=note&note=like&idTouite={$this->touite->id}'>Like le touite</a><br>";
//$aff.="<a href='main.php?action=note&note=dislike&idTouite={$this->touite->id}'>Dislike le touite</a><br>";