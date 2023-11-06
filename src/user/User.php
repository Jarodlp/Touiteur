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

    //l'utilisateur publie un touite
    public function publieTouite(string $texte, array $tags) : Touite {
        return new Touite($texte, $this->username, $tags);
    }
}