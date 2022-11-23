<?php

class CheeseBuilder {

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

    public function createCheese() {
        return new Cheese(strip_tags($this->data['name']), strip_tags($this->data['region']), $this->data['year'], $_SESSION['user']->getName());
    }

    public function isValid() {
        $this->error= array('name' => '',
            'region' => '',
            'year' => '',
            'image' => ''
        );

        $a = $this->createCheese();

        if ($a->getName() != "" && $a->getRegion() != "" && $a->getYear() > 0) {
            return true;
        }
        else {
            if ($a->getName() === "" || strip_tags($a->getName()) !== $a->getName()) {
                $this->error['name'] = "Le champ 'Nom du fromage' est invalide.";
            }
            if ($a->getRegion() === "" || strip_tags($a->getRegion()) !== $a->getRegion()) {
                $this->error['region'] = "Le champ 'Region du fromage' est invalide.";
            }
            if ($a->getYear() <= 0) {
                $this->error['year'] = "Le champ 'Année de creation du fromage' est invalide.";
            }
            return false;
        }
    }

    public function isImageValid($image, $id) {
        $type = str_replace('image/','', $image['type']);
        if(!exif_imagetype($image['tmp_name'])) {
            $this->error['image'] = "Ce n'est pas une image.";
            return false;
        }
        if($type != "jpg" && $type != "png" && $type != "jpeg" && $type != "gif" ) {
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
