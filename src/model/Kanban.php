<?php

/*
Objet pour les kanbans.

$name est son nom.
$desc est sa description.
$public = 0 : Le kanban est public, sinon il est privé.
$creator est le créateur du kanban.
$members sont les membres du kanban.

$columns : Liste de colonnes.
$columns[i]['id'] : id de la colonne i.
$columns[i]['name'] : nom de la colonne i.
$columns[i]['tasks'] : liste de tâches de la colonne i.
$columns[i]['tasks'][$id]['name'] : nom de la tâche d'id $id de la colonne i.
$columns[i]['tasks'][$id]['id'] : id de la tâche d'id $id de la colonne i.
$columns[i]['tasks'][$id]['affectation'] : compte affecté à la tâche d'id $id de la colonne i.

...
*/

class Kanban {

    private $name;
    private $desc;
    private $public;
    private $creator;
    private $image;
    private $members;
    private $columns;

    public function __construct($name, $desc, $public, $creator, $members=null, $image = null, $columns=null) {
        $this->name = $name;
        $this->region = $desc;
        $this->public = $public;
        $this->creator = $creator;
        $this->image = $image;
        $this->members = $members;
        $this->columns = $columns;
    }

    public function getName() {
        return $this->name;
    }

    public function getDesc() {
        return $this->desc;
    }

    public function isPublic() {
        if($this->public == 0) {
            return true;
        }
        return false;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function getImage() {
        return $this->image;
    }

    public function getMembers() {
        return $this->members;
    }

    public function getColumns() {
        return $this->columns;
    }
}