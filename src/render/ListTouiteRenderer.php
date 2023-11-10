<?php

namespace iutnc\touiteur\render;

use \iutnc\touiteur\list\ListTouite;
use \iutnc\touiteur\render\TouiteRenderer;

class ListTouiteRenderer implements Renderer{
    private ListTouite $list;

    public function __construct(ListTouite $liste){
        $this->list = $liste;
    }

    public function render(int $selector):String{
        $action = $_GET["action"];
        $affichage = "";
        //système de pagination au-delà de 10 touites.
        if($this->list->length>10){
            //$_GET["page"] n'est set que si nous ne sommes pas sur la première page, donc si le système de pagination est activé.
            //il faut donc récupérer les 10 touites (ou moins) concernés.
            $page = $_GET["page"];
            if($page>1){
                if($this->list->length <= ($page*10)){
                    //cas où nous sommes sur la dernière page
                    $previousPage=$_GET["page"]-1;
                    $maxIndex=$this->list->length-1;
                    for($i=$previousPage*10;$i<$maxIndex+1;$i++){
                        $affichage.=(new TouiteRenderer($this->list->touites[$i]))->render($selector);
                    }
                    $affichage.="<form id='form1' method='GET' action='main.php'>".
                            "<button type='submit' name='page' value={$previousPage} id='nextPage'>Previous page</button>". 
                            "<input type='hidden' name='action' value='{$action}'>".
                            "</form><br>";
                } else{
                    //cas où il y a plus de 10 touites et nous ne sommes pas sur la première page
                    $nextPage = $_GET["page"]+1;
                    $previousPage = $_GET["page"]-1;

                    //maxIndex représente l'indice du dernier touite présent sur la page actuelle.
                    //Ex : s'il y a 24 touites et que nous sommes sur la page 2, les touites concernés vont du 10ème au 19ème. (car index d'un tableau débute à 0).
                    //Donc le dernier touite concerné est : 2*10-1=19ème touite. Donc maxIndex=19. 
                    $maxIndex = $page*10-1;
                    if($this->list->length<$maxIndex+1){
                        $maxIndex = $this->list->length-1;
                    }
                    for($i = $maxIndex-9; $i < $maxIndex+1; $i++){
                        $affichage.=(new TouiteRenderer($this->list->touites[$i]))->render($selector);
                    }
                    $affichage.="<form id='form1' method='GET' action='main.php'>".
                            "<button type='submit' name='page' value={$previousPage} id='previousPage'>Previous Page</button>". 
                            "<button type='submit' name='page' value={$nextPage} id='nextPage'>Next Page</button>". 
                            "<input type='hidden' name='action' value='{$action}'>".
                            "</form><br>";
                }
            } else{
                //cas où il y a plus de 10 touites mais nous sommes sur la première page (accueil)
                for($i=0;$i<10;$i++){
                $affichage.=(new TouiteRenderer($this->list->touites[$i]))->render($selector);
                }
                $affichage.="<form id='form1' method='GET' action='main.php'>".
                            "<button type='submit' name='page' value=2 id='nextPage'>Next Page</button>". 
                            "<input type='hidden' name='action' value='{$action}'>".
                            "</form><br>";
            }
        } else{
            //cas normal où il y 10 touites ou moins
            foreach($this->list->touites as $touite){
                $affichage.=(new TouiteRenderer($touite))->render($selector);
            }
        }
        return $affichage;
    }
}