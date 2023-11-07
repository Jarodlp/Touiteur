<?php

namespace iutnc\touiteur\touite;

class Touite {
    protected int $id;
    protected string $cheminImage;
    protected string $texte;
    protected string $auteur;
    protected \date $date;
    protected array $tags;
    protected int $note;

<<<<<<< HEAD
    public function __construct(string $texte, string $auteur, array $tags=[]){
=======
    public function __construct(int $id, string $texte, string $auteur, array $tags){
        $this->id = $id;
>>>>>>> 9ba18bb441232b92267ff38571f796324847284c
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