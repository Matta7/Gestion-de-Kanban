<?php

/*
Objet colonne contenue dans un Kanban :

$name : nom de la colonne.
$tasks : liste de tâches de la colonne.
*/

class Column {

    private $name;
    private $tasks;

    public function __construct($name, $tasks = null) {
        $this->name = $name;
        $this->tasks = $tasks;
    }

    public function getName() {
        return $this->name;
    }

    public function getTasks() {
        return $this->tasks;
    }
}

?>