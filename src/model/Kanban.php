<?php

/*
Objet pour les kanbans.

$name est son nom.
$desc est sa description.
$public = 0 : Le kanban est public, sinon il est privé.
$creator est le créateur du kanban.
$members sont les membres du kanban.


*/

class Kanban {

    private $name;
    private $desc;
    private $public;
    private $creator;
    private $image;
    private $members;

    public function __construct($name, $desc, $public, $creator, $members=null, $image = null) {
        $this->name = $name;
        $this->region = $desc;
        $this->public = $public;
        $this->creator = $creator;
        $this->image = $image;
        $this->members = $members;
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
}
