<?php

namespace iutnc\touiteur\touite;

class Touite {
    protected int $id;
    protected string $cheminImage;
    protected string $texte;
    protected string $username;
    protected \date $date;
    protected array $tags;
    protected int $note;

    public function __construct(int $id, string $texte, string $username, array $tags=[]){
        $this->id = $id;
        $this->texte= $texte;
        $this->username = $username;
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