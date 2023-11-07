<?php

namespace iutnc\touiteur\db;

class ConnectionFactory {
    static ?array $parametres = null;
    static ?\PDO $connexion=null;

    public static function setConfig($file) {
        if (!isset(self::$parametres)) {
            self::$parametres = parse_ini_file($file);
        }
    }    
    
    public static function makeConnection() : \PDO {
        if (!isset(self::$connexion)) {
            self::$connexion = new \PDO(self::$parametres['driver'].':host='.self::$parametres['host'].';dbname='.self::$parametres['database'].';charset=utf8',self::$parametres['username'],self::$parametres['password']);  
        }
        return self::$connexion;
    }
}