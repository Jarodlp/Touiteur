<?php

namespace iutnc\touiteur\list;

use \iutnc\touiteur\touite\Touite;

class ListTouite{
    
    private int $length;
    private array $touites;

    public function __construct(array $list=[]) {
        $this->touites=$list;
        if($this->touites==[]){
            $this->length=0;
        } else{
            $this->length=sizeof($this->touites);
        }
    }

    public function addTouite(Touite $touite) : void{
        array_push($this->touites,$touite);
        $this->length+=1;
    }

    public function addList(array $tab) : void {
        for ($i = 0; i < sizeof($this->touites); $i++){
            for($j = 0; j < sizeof($tab); $j++){
                if($tab[$j] === $this->touites[$i]){
                    array_splice($tab, $j, 1);
                }
            }
        }
        $this->touites=array_merge($this->touites,$tab);
    }

    public function __get(string $attr) : mixed {
        if (property_exists($this, $attr)){
            return $this->$attr;
        } else{
            throw new \iutnc\touiteur\exception\InvalidNameException("$attr : invalid property");
        }
    }
}