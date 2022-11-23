<?php

interface CheeseStorage {
    public function read($id);
    public function readAll();
    public function create(Cheese $a);
    public function delete($id);
    public function update($id, $cheese);
}
