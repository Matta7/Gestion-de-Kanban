<?php

class KanbanBuilder {

    protected $data;
    protected $error;

    public function __construct($data = null, $error = null) {
        $this->data = $data;
        $this->error = $error;
    }

    public function getData() {
        return $this->data;
    }

    public function getError() {
        return $this->error;
    }

    public function createKanban() {
        return new Kanban(strip_tags($this->data['name']), strip_tags($this->data['desc']), $this->data['public'], $_SESSION['user']->getLogin());
    }

    public function isValid() {
        $this->error= array('name' => '',
            'desc' => '',
            'image' => ''
        );

        $k = $this->createKanban();
        $valid = true;

        if ($k->getName() === "" || $k->getName() !== $this->data['name']) {
            $this->error['name'] = "Le champ 'Nom' est invalide.";
            $valid = false;
        }
        if ($k->getDesc() === "" || $k->getDesc() !== $this->data['desc']) {
            $this->error['desc'] = "Le champ 'Description' est invalide.";
            $valid = false;
        }
        return $valid;
    }

    public function isImageValid($image, $id) {
        $type = str_replace('image/','', $image['type']);
        if(!exif_imagetype($image['tmp_name'])) {
            $this->error['image'] = "Ce n'est pas une image.";
            return false;
        }
        if($type != "jpg" || $type != "png" || $type != "jpeg" || $type != "gif" ) {
            $this->error['image'] = "Les types d'image autorisés sont JPG, JPEG, PNG & GIF.";
            return false;
        }
        if ($image['size'] > 500000) {
            $this->error['image'] = "L'image est trop volumineuse.";
            return false;
        }
        return true;
    }
}
