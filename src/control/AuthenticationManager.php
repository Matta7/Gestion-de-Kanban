<?php

class AuthenticationManager {

    protected $accountDTB;

    public function __construct($accountDTB) {
        $this->accountDTB = $accountDTB;
    }

    // Connecte l'utilisateur en mettant le compte dans la session.
    public function connectUser($login, $password) {
        $account = $this->accountDTB->checkAuth($login, $password);
        if($account != null) {
            $_SESSION['user'] = $account;
        }
    }

    // Fonction qui retourne true si l'utilisateur est connecté.
    public function isUserConnected() {
        if(key_exists('user', $_SESSION)) {
            return true;
        }
        return false;
    }

    // Fonction qui retourne true si un admin est connecté.
    public function isAdminConnected() {
        if(key_exists('user', $_SESSION)) {
            if($_SESSION['user']->getStatus() == 'admin') {
                return true;
            }
        }
        return false;
    }

    // Fonction qui retourne le nom de l'utilisateur connecté.
    public function getUserName() {
        return $_SESSION['user']->getName();
    }

    public function getUserLogin() {
        return $_SESSION['user']->getLogin();
    }

    // Fonction qui déconnecte l'utilisateur.
    public function disconnectUser() {
        unset($_SESSION['user']);
    }

    // Fonction qui inscrit un utilisateur.
    public function registration($name, $login, $password) {
        return $this->accountDTB->registration($name, $login, $password);
    }

    public function getAccount($login) {
        return $this->accountDTB->getAccount($login);
    }
}