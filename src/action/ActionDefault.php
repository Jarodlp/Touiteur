<?php 

namespace iutnc\touiteur\action;

class ActionDefault extends Action {
    public function execute () : string {
        $aff = "";
        $aff.="Bienvenue<br>";
        return $aff;
    }
}