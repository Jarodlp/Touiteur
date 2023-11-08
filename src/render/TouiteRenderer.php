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
        $aff.="<a href='main.php?action=display-touite&param=one&id=".$this->touite->id."'>".$this->touite->texte."</a><br>";
        return $aff;
    }

    //affichage complet avec toutes les infos
    private function renderLong() : string {
        $aff = "";
        $aff.=$this->touite->texte."<br>";
        //on affiche l'utilisateur en lien pour afficher les touites de l'utilisateur
        $aff.="<a href='main.php?action=display-touite&param=user&username=".$this->touite->username."'>Auteur : ".$this->touite->username."</a><br>";
        //on affiche le lien pour follow l'utilisateur
        $aff.="<a href='main.php?action=follow&username={$this->touite->username}'>Follow {$this->touite->username}</a><br>";
        //on affiche tout les tags en lien pour afficher les touites de ces tags
        foreach ($this->touite->tags as $key => $value) {
            $aff.="<a href='main.php?action=display-touite&param=tag&title=".$value."'>Tag : ".$value."</a><br>";
        }
        return $aff;
    }
}