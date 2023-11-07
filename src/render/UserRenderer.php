<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\user\User;

class UserRenderer {
    const COMPACT = 1;
    const LONG = 2;
    public User $user;

    public function __construct(User $user) {
        $this->user = $user;
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
        return "<h1>{$this->user->firstName}</h1><br>";
    }

    //affichage complet avec toutes les infos
    public function renderLong() : string {
        return "<h1>{$this->user->firstName} {$this->user->lastName}</h1><br>";
    }
}