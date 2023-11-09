<?php

namespace iutnc\touiteur\render;

class ListTouiteRenderer implements Renderer{
    private \iutnc\touiteur\list\ListeTouite $list;

    public function __construct(\iutnc\touiteur\list\ListeTouite $l){
        $this->list=$l;
    }

    public function render(int $selector):String{
        $affichage
    }
}