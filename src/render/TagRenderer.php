<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\touite\Tag;

class TagRenderer implements Renderer{
    
    protected Tag $tag;

    public function __construct(Tag $tag){
        $this->tag=$tag;
    }

    //affichage du tag
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
        return $this->tag->title."<br>";
    }

    //afichage long
    public function renderLong() : string {
        return "<h1>{$this->tag->title} : {$this->tag->description}</h1><br>";
    }
}