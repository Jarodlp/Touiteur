<?php

namespace iutnc\touiteur\user;

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
}