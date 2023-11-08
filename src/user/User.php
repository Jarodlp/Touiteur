<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
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
    public function publieTouite(string $texte, array $tags=[]) : Touite {
        //On regarde les tags présents dans le texte et on les ajoute à la BD si ils n'existent pas
        $auteur = $this->username;
        $connexion = ConnectionFactory::makeConnection();
        // on insère le touite dans la bd et on récupère son id ensuite
        $statement = $connexion->prepare('insert into touite(username,text,dateTouite) values (?,?,sysdate())');
        $statement->bindParam(1, $auteur);
        $statement->bindParam(2, $texte);
        $statement->execute();

        // On part du principe qu'un même utilisateur ne va pas publier deux fois le même message
        $statement = $connexion->prepare('select idTouite from touite where username = ? and text = ?');
        $statement->bindParam(1, $auteur);
        $statement->bindParam(2, $texte);
        $statement->execute();
        $result = $statement->fetch();
        $idTouite = $result[0];

        // boucles pour les tags
        foreach ($tags as $tag){
            // on recherche si le tag existe déjà dans la BD
            $statement = $connexion->prepare('SELECT COUNT(*) FROM tag WHERE title = ?');
            $statement->bindParam(1, $tag);
            $statement->execute();
            $result = $statement->fetch();
            // S'il existe pas, on le crée
            if ($result[0] == 0){
                $statement = $connexion->prepare('insert into tag(title) values (?)');
                $statement->bindParam(1, $tag);
                $statement->execute();
            }
            // On récupère l'id du tag
            $statement = $connexion->prepare('select idTag from tag where title = ?');
            $statement->bindParam(1, $tag);
            $statement->execute();
            $result = $statement->fetch();
            $idTag = $result[0];
            // Et ensuite on insère la liaison entre le touite et son/ses tags dans la table touiteTag
            $statement = $connexion->prepare('insert into touiteTag values (?,?)');
            $statement->bindParam(1, $idTouite);
            $statement->bindParam(2, $idTag);
            $statement->execute();
        }
        return new Touite($idTouite,$texte,$auteur,$tags);
    }
}