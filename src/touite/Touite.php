<?php

namespace iutnc\touiteur\touite;
use \iutnc\touiteur\db\ConnectionFactory;

class Touite {
    protected int $id;
    protected string $cheminImage;
    protected string $texte;
    protected string $username;
    protected \date $date;
    protected array $tags;
    protected int $note;

    public function __construct(int $id, string $texte, string $username, array $tags=[], int $note = 0, string $cheminImage=""){
        $this->id = $id;
        $this->texte= $texte;
        $this->username = $username;
        $this->tags = $tags;
        $this->note = $note;
        $this->cheminImage = $cheminImage;
    }

    public static function note(String $nomNote, int $id) : string{
        $aff ="";

        //maintenant je vérifie que l'utilisateur est bien connecté, sinon il ne peut pas like
        if(isset($_SESSION['user'])){
            $user = unserialize($_SESSION["user"]);
            $username = $user->username;
            $idTouite = $id;
            //si l'utilisateur like le touite :
            if ($nomNote == "like") {
                $note = 1;
            }
            //si l'utilisateur dislike le touite :
            else if ($nomNote == "dislike"){
                $note = -1;
            }
            //on teste si l'utilisateur à déjà like ou dislike
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT COUNT(*), touitenote.note FROM touitenote WHERE touitenote.username = ? AND touitenote.idTouite = ?";
            $statement = $connexion->prepare($query);
            $statement->bindParam(1, $username);
            $statement->bindParam(2, $idTouite);
            $statement->execute();
            $result = $statement->fetch();
            $notePresente = ($result[0] == 1);
            //si l'ulitilisateur à déjà like ou dislike, on modifie la table
            if ($notePresente) {
                //la note déjà présente en BD
                $previousNote = $result[1];
                //on teste si la note actuellement effectuée est la même déjà présente dans la BD, si oui on l'enlève
                if ($note == $previousNote) {
                    $query = "DELETE FROM touitenote WHERE idTouite = ? AND username = ?";
                    $statement = $connexion->prepare($query);
                    $statement->bindParam(1, $idTouite);
                    $statement->bindParam(2, $username);
                    $statement->execute();
                    if ($note == 1) {
                        $aff.="Vous avez enlevé votre like de ce touite<br>";
                    }
                    else {
                        $aff.="Vous avez enlevé votre dislike de ce touite<br>";
                    }
                }
                else {
                    $query = "UPDATE touitenote SET note = ? WHERE idTouite = ? AND username = ?";
                    $statement = $connexion->prepare($query);
                    $statement->bindParam(1, $note);
                    $statement->bindParam(2, $idTouite);
                    $statement->bindParam(3, $username);
                    $statement->execute();
                    if ($note == 1) {
                        $aff.="Vous avez like ce touite<br>";
                    }
                    else {
                        $aff.="Vous avez dislike ce touite<br>";
                    }
                }
            }
            //sinon on ajoute dans la table le like ou dislike
            else {
                $query = "INSERT INTO touitenote VALUES (?, ?, ?)";
                $statement = $connexion->prepare($query);
                $statement->bindParam(1, $username);
                $statement->bindParam(2, $idTouite);
                $statement->bindParam(3, $note);
                $statement->execute();
                if ($note == 1) {
                    $aff.="Vous avez like ce touite<br>";
                }
                else {
                    $aff.="Vous avez dislike ce touite<br>";
                }
            }
        }
        else{
            $aff.="Vous n'êtes pas connecté, vous ne pouvez pas like le touite<br>";
        }
        return $aff;
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