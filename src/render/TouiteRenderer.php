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
        $aff.="<a href='index.php?action=display-touites&id=".$this->touite->id."'>".$this->touite->texte."</a><br>";
        return $aff;
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        $aff = "";
        $aff.=$this->touite->texte."<br>";
        $aff.="<a href='index.php?action=display-user&id=".$this->touite->auteur."'>".$this->touite->auteur."</a><br>";
        foreach ($this->touite->tags as $key => $value) {
            $aff.="<a href='index.php?action=display-tag&id=".$this->touite->auteur."'>".$value->title."</a><br>";
        }
        return $aff;
    }
}