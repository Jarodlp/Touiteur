<?php

namespace iutnc\touiteur\render;
use \iutnc\touiteur\touite\Touite;

class TouiteRenderer {
    const COMPACT = 1;
    const LONG = 2;
    public Touite $touite;

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
        return "<h1>{$this->touite->texte}</h1><br>";
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        return "<h1>{$this->touite->texte}{$this->touite->auteur}</h1><br>";
    }
}