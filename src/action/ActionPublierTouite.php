<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\TouiteRenderer;

class ActionPublierTouite extends Action {

    public function execute(): string
    {
        $aff = "";
        if ($this->http_method == "GET") {
            $aff.='<form id="add-user" method="POST" action="?action=publier-touite">
                <input type="" name="touite" placeholder="<votre touite>">
                <button type="submit">Publier</button>
                </form>';
        }
        else if ($this->http_method == "POST") {
            $texte = $_POST["touite"];
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
                if ($char === " "){
                    $bool = false;
                    $tags[] = $tag;
                }
            }


            $user = unserialize($_SESSION["user"]);
            $touite = $user->publieTouite($texte,$tags);


            $renderer = new TouiteRenderer($touite);

            $aff = "Votre tweet a bien été publié <br>";

            $aff .= $renderer->render(2);

        }
        return $aff;
    }
}