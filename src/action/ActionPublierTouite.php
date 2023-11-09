<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\TouiteRenderer;

class ActionPublierTouite extends Action {

    public function execute(): string
    {
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
            $texte = $_POST["touite"];

            if (isset($_FILES['inputfile'])) {
                $filename = uniqid();
                $chemin = 'images/' . $filename . '.png';
                $dest = __DIR__ . '/../../' . $chemin;
                move_uploaded_file($_FILES['inputfile']['tmp_name'], $dest);
            } else {
                $chemin = "pasDimage";
            }


            // on divise le texte pour séparer les tags du contenu (on suppose que les tags sont situés à la fin d'un touite)
            $str = str_split($texte);
            // on supprime le texte pour garder que les tags
            $tags = [];
            $bool = false;
            $tag = "";
            foreach ($str as $char){
                if ($bool){
                    $tag .= $char;
                }
                if ($char === "#"){
                    $bool = true;
                }
                if ($char === " " && $bool){
                    $bool = false;
                    $tags[] = $tag;
                    $tag = "";
                }
            }

            if ($bool){
                $tags[] = $tag;
            }

            $user = unserialize($_SESSION["user"]);
            $touite = $user->publieTouite($texte,$chemin,$tags);


            $renderer = new TouiteRenderer($touite);

            $aff = "Votre tweet a bien été publié <br>";

            $aff .= $renderer->render(2);

        }
        return $aff;
    }
}