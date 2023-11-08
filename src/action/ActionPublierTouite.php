<?php

namespace iutnc\touiteur\action;

class ActionPublierTouite extends Action {

    public function execute(): string
    {
        $aff = "";
        if ($this->http_method == "GET") {
            $aff.='<form id="add-user" method="POST" action="?action=publier-touite">
                <input type="text" name="touite" placeholder="<votre touite>">
                <button type="submit">Connexion</button>
                </form>';
        }
        else if ($this->http_method == "POST") {
            $texte = $_POST["touite"];
            // on divise le texte pour séparer les tags du contenu (on suppose que les tags sont situés à la fin d'un touite)
            $str = explode(" #",$texte,10);
            // on supprime le texte pour garder que les tags
            unset($str[0]);

            $user = $_SESSION['user'];
            $user->publieTouite($texte,$str);


        }
        return $aff;
    }
}