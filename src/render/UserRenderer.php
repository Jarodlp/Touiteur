<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\user\User;

class UserRenderer implements Renderer{
    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    //affichage du user
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
        return "<h1>{$this->user->username}</h1><br>";
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        return "<h1>{$this->user->firstName} {$this->user->lastName}</h1><br>";
    }

    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}