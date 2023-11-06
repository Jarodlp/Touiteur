<?php

namespace iutnc\touiteur\action;

class ActionAddUser{

    public function execute(String $request){
        $content="<form id='form1' method='POST' action='?action=add-user'>".
            "<input type='text' name='username' placeholder='Username'>".
            "<input type='password' name='password' placeholder='Mot de passe'>".
            "<input type='email' name='email' placeholder='Email'>".
            "<input type='text' name='firstname' placeholder='First name'>".
            "<input type='text' name='lastname' placeholder='Last Name'>".
            "<button type='submit' name='valider_inscription'>Inscription</button>".
          "</form>";
        if($request==="POST"){
            
        }
    }
}