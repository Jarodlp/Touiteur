<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\touite\Tag;

class TagRenderer implements Renderer{
    
    protected Tag $tag;

    public function __construct(Tag $tag){
        $this->tag = $tag;
    }

    //affichage du tag
    public function render(int $selector): string {
        if ($selector == self::COMPACT) {
            return $this->renderCompact();
        }
        else if ($selector == self::LONG) {
            return $this->renderLong();
        } else{
            return "erreur";
        }
    }

    //affichage simple
    private function renderCompact() : string {
        return $this->tag->title."<br>";
    }

    //afichage long
    private function renderLong() : string {
        $aff="";
        $aff.="Titre du tag : {$this->tag->title}<br>";
        $aff.="Description : ".$this->tag->description."<br>";
        //possibilité de suivre ce tag
        $aff.="<a href='main.php?action=followTag&tagName={$this->tag->title}'>Suivre ce tag</a><br>";
        return $aff;
    }
}