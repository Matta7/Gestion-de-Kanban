<?php

/*
Objet compte. Est utilisé pour afficher la vue.
*/

class Account {

    protected $name;
    protected $login;
    protected $password;
    protected $status;

    public function __construct($name, $login, $password, $status) {
        $this->name = $name;
        $this->login = $login;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->status = $status;
    }

    public function getName() {
        return $this->name;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getStatus() {
        return $this->status;
    }


}