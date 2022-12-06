<?php

require_once('model/Account.php');
require_once('model/AccountStorage.php');

/*
Classe qui gère tout ce qui est base de données MySQL au niveau des comptes
Nous avons principalement des requêtes préparées, visant à renforcer la sécurité (l'utilisateur ne pouvant injecter du code).
*/

class AccountStorageMySQL implements AccountStorage {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fonction pour se connecter, on prend le compte de login $login et on vérifie le mot de passe.
    public function checkAuth($login, $password) {

        $requete = "SELECT * FROM accounts WHERE accounts . login = :login";
        $stmt = $this->db->prepare($requete);
        $data = array(':login' => $login);

        $stmt->execute($data);
        $resultatRequete = $stmt->fetch();

        if($resultatRequete != null) {
            if(password_verify($password, $resultatRequete['password'])) {
                return new Account($resultatRequete['name'], $login, $password, $resultatRequete['status']);
            }
        }
        return null;
    }

    // Fonction pour s'inscrire.
    public function registration($name, $login, $password) {

        // On vérifie que l'utilisateur n'a pas rentré quelque chose comme une balise html (<balise>).
        if(strip_tags($name) !== $name) {
            return false;
        }

        // On vérifie que le compte n'existe pas.
        $requete = "SELECT * FROM accounts";
        $resultatRequete = $this->db->query($requete)->fetchAll();

        foreach($resultatRequete as $key => $value) {
            if($value['login'] === $login) {
                return false;
            }
        }

        // Si le compte n'existe pas, on insert le compte avec les données de l'utilisateur.
        $requete = "INSERT INTO accounts (name, login, password) VALUES (:name, :login, :password) ";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $name,
            ':login' => $login,
            ':password' => password_hash($password, PASSWORD_BCRYPT)
        );
        $stmt->execute($data);

        return true;
    }

    public function getAccount($login) {

    }

    public function getAllAccounts() {
        
    }
}