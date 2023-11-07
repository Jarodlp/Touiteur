<?php

namespace iutnc\touiteur\touite;

class Tag{
    protected int $id;
    protected String $title;
    protected String $description;

    public function __construct(int $id, String $title, String $description=""){
        $this->id = $id;
        $this->title=$title;
        $this->description=$description;
    }

    public function addDescription($description){
        $this->description=$description;
    }

    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}