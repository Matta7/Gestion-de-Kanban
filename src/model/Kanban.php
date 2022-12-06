<?php

/*
Objet pour les kanbans.

$name : nom du kanban.
$desc : description du kanban.
$public : objet public ou non.
$creator : créateur du kanban.
$members : liste de membres (Account) du kanban.
$columns : liste de colonnes (Columns).
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