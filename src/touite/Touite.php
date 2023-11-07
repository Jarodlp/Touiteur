<?php

namespace iutnc\touiteur\touite;

class Touite {
    protected string $cheminImage;
    protected string $texte;
    protected string $auteur;
    protected \date $date;
    protected array $tags;
    protected int $note;

    public function __construct(string $texte, string $auteur, array $tags=[]){
        $this->texte= $texte;
        $this->auteur = $auteur;
        $this->tags = $tags;
        //le touite n'a pas de note au début
        $this->note = 0;
    }

    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}