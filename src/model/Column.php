<?php

/*
Objet colonne contenue dans un Kanban :

$name : nom de la colonne.
$tasks : liste de tâches de la colonne.
*/

class Column {

    private $id;
    private $name;
    private $tasks;

    public function __construct($id, $name, $tasks = null) {
        $this->id = $id;
        $this->name = $name;
        $this->tasks = $tasks;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getTasks() {
        return $this->tasks;
    }
}

?>