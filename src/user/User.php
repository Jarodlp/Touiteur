<?php

namespace iutnc\touiteur\user;

use \iutnc\touiteur\touite\Touite;

class User{
    protected String $username;
    protected String $password;
    protected String $email;
    protected String $firstName;
    protected String $lastName;

    public function __construct($username,$password,$email,$firstName,$lastName){
        $this->username=$username;
        $this->password=$password;
        $this->email=$email;
        $this->firstName=$firstName;
        $this->lastName=$lastName;
    }

    //getter magique
    public function __get( string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }

    //l'utilisateur publie un touite
    public function publieTouite(string $texte, array $tags) : Touite {
        return new Touite($texte, $this->username, $tags);
    }
}