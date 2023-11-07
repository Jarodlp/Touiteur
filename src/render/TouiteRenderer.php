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
        }
    }

    //affichage simple 
    public function renderCompact() : string {
        $aff="";
        $aff.="<a href='main.php?action=display-touite&id=".$this->touite->id."'>".$this->touite->texte."</a><br>";
        return $aff;
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        $aff = "";
        $aff.=$this->touite->texte."<br>";
        $aff.="<a href='main.php?action=display-user&username=".$this->touite->username."'>Auteur : ".$this->touite->username."</a><br>";
        foreach ($this->touite->tags as $key => $value) {
            $aff.="<a href='main.php?action=display-tag&title=".$value."'>Tag : ".$value."</a><br>";
        }
        return $aff;
    }
}