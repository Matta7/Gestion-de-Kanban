<?php

class Cheese {

    private $name;
    private $region;
    private $year;
    private $creator;
    private $image;

    public function __construct($name, $region, $year, $creator, $image = null) {
        $this->name = $name;
        $this->region = $region;
        $this->year = $year;
        $this->creator = $creator;
        $this->image = $image;
    }

    public function getName() {
        return $this->name;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getYear() {
        return $this->year;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function getImage() {
        return $this->image;
    }
}
