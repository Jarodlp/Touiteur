<?php

namespace iutnc\touiteur\action;

class ActionAfficherTags extends Action {
    public function execute () :string {
        $affichage = "";

        //données de base
       $tag1=new \iutnc\touiteur\touite\Tag("Ciel","Le ciel est bleu");
       $tag2=new \iutnc\touiteur\touite\Tag("Tibere","Tibere est muscle");
       $tag3=new \iutnc\touiteur\touite\Tag("Jarod","Jarod n'aime pas Karmine");

        

        $tag1render = new \iutnc\touiteur\render\TagRenderer($tag1);
        $tag2render = new \iutnc\touiteur\render\TagRenderer($tag2);
        $tag3render = new \iutnc\touiteur\render\TagRenderer($tag3);
        print $tag1render->render(2);
        print $tag2render->render(2);
        print $tag3render->render(2);

        return $affichage;
    }
}