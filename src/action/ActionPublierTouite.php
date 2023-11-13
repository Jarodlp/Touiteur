<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\db\ConnectionFactory;


class ActionPublierTouite extends Action {

    public function execute(): string {
        $aff = "";
        if ($this->http_method == "GET") {
            if (isset($_SESSION['user'])) {
                $aff .= '<form id="add-user" enctype="multipart/form-data" method="POST" action="?action=publier-touite">
                <input type="text" name="touite" placeholder="<votre touite>">
                <p><input type="file" name="inputfile" accept=".png,.jpeg,.jpg,.gif"></p>
                <button type="submit">Publier</button>
                </form>';
            } else {
                $aff = "Veuillez vous connectez afin de publier un touite";
            }
        }
        else if ($this->http_method == "POST") {
            $texte=$_POST["touite"];
            //$texte = filter_var($_POST["touite"],FILTER_SANITIZE_SPECIAL_CHARS);
            //si le texte est vide on réaffiche le formulaire
            if (empty($texte)) {
                $aff.="Vous n'avez pas rentré de texte dans votre touite";
                $aff .= '<form id="add-user" enctype="multipart/form-data" method="POST" action="?action=publier-touite">
                <input type="text" name="touite" placeholder="<votre touite>">
                <p><input type="file" name="inputfile" accept=".png,.jpeg,.jpg,.gif"></p>
                <button type="submit">Publier</button>
                </form>';
            }
            //sinon on peut publier le touite
            else {
                $connexion = ConnectionFactory::makeConnection();
                //on déplace l'image dans le répertoire si l'user en a mis une
                if (($_FILES['inputfile']['tmp_name'] !== "")) {
                    $filename = uniqid();
                    $cheminImage = 'images/'.$filename.'.png';
                    $dest = __DIR__ . '/../../'.$cheminImage;
                    move_uploaded_file($_FILES['inputfile']['tmp_name'], $dest);
                    //$cheminImage = '\\..\\..\\images\\'.$filename.'.png';
                }
                else {
                    $cheminImage = "";
                }

                // on divise le texte pour séparer les tags du contenu (on suppose que les tags sont situés à la fin d'un touite)
                $str = str_split($texte);
                // on supprime le texte pour garder que les tags
                $tags = [];
                $tagPresent = false;
                $tag = "";
                foreach ($str as $char) {
                    if ($char === " " && $tagPresent) {
                        if(preg_match("#[\W]#", $tag) == 0) {
                            $tags[] = $tag;
                        }
                        $tagPresent = false;
                        $tag = "";
                    }
                    else if ($tagPresent) {
                        $tag .= $char;
                    }
                    else if ($char === "#") {
                        $tagPresent = true;
                    }
                }
                //si le tag est la toute fin du texte
                if ($tagPresent && preg_match("#[\W]#", $tag) == 0){
                    $tags[] = $tag;
                }

                $user = unserialize($_SESSION["user"]);
                $auteur = $user->username;
                //On regarde les tags présents dans le texte et on les ajoute à la BD si ils n'existent pas
                // on insère le touite dans la bd
                $statement = $connexion->prepare('INSERT INTO touite(username,text,dateTouite) VALUES (?, ?, sysdate())');
                $statement->bindParam(1, $auteur);
                $statement->bindParam(2, $texte);
                $statement->execute();
                //on récupère l'id du touite
                $statement = $connexion->prepare('SELECT MAX(idTouite) FROM touite WHERE username = ? AND text = ?');
                $statement->bindParam(1, $auteur);
                $statement->bindParam(2, $texte);
                $statement->execute();
                $result = $statement->fetch();
                $idTouite = $result[0];

                //on ajoute l'image si elle a été rentrée
                if ($cheminImage !== "") {
                    // On insère l'image dans la bd si elle existe pas
                    $statement = $connexion->prepare('SELECT COUNT(*) FROM image WHERE fileName = ?');
                    $statement->bindParam(1, $cheminImage);
                    $statement->execute();
                    $result = $statement->fetch();
                    if ($result[0] == 0) {
                        $statement = $connexion->prepare('INSERT INTO image(fileName) values (?)');
                        $statement->bindParam(1, $cheminImage);
                        $statement->execute();
                    }

                    // Et on récupère son ID
                    $statement = $connexion->prepare('SELECT idImage FROM image WHERE fileName = ?');
                    $statement->bindParam(1, $cheminImage);
                    $statement->execute();
                    $result = $statement->fetch();
                    $idImage = $result[0];

                    // Et on insère la liaison entre le touite et son image dans la bd
                    $statement = $connexion->prepare('INSERT INTO touiteimage VALUES (?,?)');
                    $statement->bindParam(1, $idTouite);
                    $statement->bindParam(2, $idImage);
                    $statement->execute();
                }

                    foreach ($tags as $tag) {
                        // on recherche si le tag existe déjà dans la BD
                        $statement = $connexion->prepare('SELECT COUNT(*) FROM tag WHERE title = ?');
                        $statement->bindParam(1, $tag);
                        $statement->execute();
                        $result = $statement->fetch();
                        // S'il existe pas, on le crée
                        if ($result[0] == 0) {
                            $statement = $connexion->prepare('INSERT INTO tag(title, descriptionTag) VALUES (?, ?)');
                            $statement->bindParam(1, $tag);
                            $statement->bindParam(2, $tag);
                            $statement->execute();
                        }
                        // On récupère l'id du tag
                        $statement = $connexion->prepare('SELECT idTag FROM tag WHERE title = ?');
                        $statement->bindParam(1, $tag);
                        $statement->execute();
                        $result = $statement->fetch();
                        $idTag = $result[0];
                        // Et ensuite on insère la liaison entre le touite et son/ses tags dans la table touiteTag
                        try{
                            $statement = $connexion->prepare('INSERT INTO touitetag VALUES (?,?)');
                            $statement->bindParam(1, $idTouite);
                            $statement->bindParam(2, $idTag);
                            $statement->execute();
                        } catch(\PDOException $e){

                        }
                    }
                
                //on récupère les tags
                
                $texte=filter_var($texte,FILTER_SANITIZE_SPECIAL_CHARS);
                $touite = new Touite($idTouite, $texte, $auteur, $tags, 0, $cheminImage);
                $touiteRenderer = new TouiteRenderer($touite);

                $aff = "Votre tweet a bien été publié <br>";
                $aff .= $touiteRenderer->render(2);
            }
        }
        return $aff;
    }
}