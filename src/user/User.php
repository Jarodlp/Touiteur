<?php

namespace iutnc\touiteur\user;

class User{
    protected String $username;
    protected String $password;
    protected String $email;
    protected String $firstName;
    protected String $lastName;

    public function __construct($u,$pw,$mail,$fN,$lN){
        $this->username=$u;
        $this->password=$pw;
        $this->email=$mail;
        $this->firstName=$fN;
        $this->lastName=$lN;
    }
}