<?php

/*
Objet tâche contenue dans une colonne :

$desc : description de la tâche.
$affectation : personne affectée à la tâche.
$dateLimite : date limite de la tâche.
*/

class Task {

    private $id;
    private $desc;
    private $affectation;
    private $dateLimite; 

    public function __construct($desc, $affectation = null, $dateLimite = null) {
        $this->id = 0;
        $this->desc = $desc;
        $this->affectation = $affectation;
        $this->dateLimite = $dateLimite;
    }

    public function getId() {
        return $this->id;
    }

    public function getDesc() {
        return $this->name;
    }

    public function getAffectation() {
        return $this->affectation;
    }

    public function getDateLimite() {
        return $this->dateLimite;
    }
}

?>