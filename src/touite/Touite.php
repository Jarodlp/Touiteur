<?php

namespace iutnc\touiteur\touite;

class Touite {
    public string $cheminImage;
    public string $texte;
    public string $auteur;
    public \date $date;
    public array $tags;
    public int $note;

    public function __construct(string $texte, string $auteur, array $tags){
        $this->texte= $texte;
        $this->auteur = $auteur;
        $this->tags = $tags;
        //le touite n'a pas de note au début
        $this->note = 0;
    }
}