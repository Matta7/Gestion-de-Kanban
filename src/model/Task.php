<?php

/*
Objet tâche contenue dans une colonne :

$desc : description de la tâche.
$affectation : personne affectée à la tâche.
$dateLimite : date limite de la tâche.
*/

class Task {

    private $desc;

    public function __construct($desc, $affectation = null, $dateLimite = null) {
        $this->desc = $desc;
        $this->affectation = $affectation;
        $this->dateLimite = $dateLimite;
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