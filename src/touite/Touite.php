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

    public function __construct(int $id, string $texte, string $username, array $tags=[], int $note = 0){
        $this->id = $id;
        $this->texte= $texte;
        $this->username = $username;
        $this->tags = $tags;
        $this->note = $note;
    }

    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }

    public function __set(string $attr, int $val) : void {
        if (property_exists($this, $attr)){
            $this->$attr = $val;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}