<?php

namespace iutnc\touiteur\dispatch;

class Dispatcher{

    public function run(){
        if(isset($_GET['action'])){
            $action=$_GET['action'];
            switch($action){
                case "add-user":
                    $action=new iutnc\touiteur\action\ActionAddUser();
                    $content=$action->execute($_SERVER['REQUEST_METHOD']);
                    break;
                case "connect-user":
                    $action=new 
            }
        }
    }
}