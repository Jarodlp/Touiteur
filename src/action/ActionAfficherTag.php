<?php

namespace iutnc\touiteur\action;

use \iutnc\touiteur\db\ConnectionFactory;
use \iutnc\touiteur\touite\Tag;
use \iutnc\touiteur\render\TagRenderer;

class ActionAfficherTag extends Action {
    public function execute () :string {
        $affichage = "";

        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM tag WHERE tag.title = ?";
        $statment = $connexion->prepare($query);
        $statment->bindParam(1, $_GET["title"]);
        $statment->execute();
        $data = $statment->fetch();
        $tag = new Tag($data["title"], $data["descriptionTag"]);
        $tagRender = new TagRenderer($tag);
        $affichage.=$tagRender->render(2);

        return $affichage;
    }
}