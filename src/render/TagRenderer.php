<?php

namespace iutnc\touiteur\render;

class TagRenderer implements Renderer{
    
    protected \iutnc\touiteur\touite\Tag $tag;

    public function __construct(\iutnc\touiteur\touite\Tag $tag){
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
        return "<h1>{$this->tag->title}</h1><br>";
    }

    //afichage long
    public function renderLong() : string {
        return "<h1>{$this->tag->title} : {$this->tag->description}</h1><br>";
    }
}