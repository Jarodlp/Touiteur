<?php

namespace iutnc\touiteur\touite;

use \iutnc\touiteur\db\ConnectionFactory;

class Tag{
    protected String $title;
    protected String $description;

    public function __construct(String $title, String $description=""){
        $this->title=$title;
        $this->description=$description;
    }

    public function addDescription($description){
        $this->description=$description;
    }

    public static function tagExist(String $title): bool{
        $exist=false;
        $connexion = ConnectionFactory::makeConnection();
        $query="SELECT * FROM tag WHERE tag.title=?";
        $statement=$connexion->prepare($query);
        $statement->bindParam(1,$title);
        $statement->execute();
        $donnee = $statement->fetch();
        return is_array($donnee);
    }

    public function __get(string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}