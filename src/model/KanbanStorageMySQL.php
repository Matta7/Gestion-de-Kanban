<?php

require_once('model/CheeseBuilder.php');
require_once('model/Cheese.php');
require_once('model/CheeseStorage.php');

class CheeseStorageMySQL implements CheeseStorage {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Permet de récupérer l'objet d'identifiant $id.
    public function read($id) {
        $requete = "SELECT * FROM kanban WHERE idKanban = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $id);
        $stmt->execute($data);

        $resultatRequeteK = $stmt->fetch();
        
        //Search for members
        $requete = "SELECT * FROM membres WHERE idKanban = :id";
        $stmt = this->db->prepare($requete);
        $stmt->execute($data);

        $resultatRequeteM = $stmt->fetchAll();
        $i = 0;
        foreach($resultatRequeteM as $key => $value){
            $membres[$i] = $value['login'];
            $i++;
        }

        // Créations des colonnes
        $requete = "SELECT * FROM colonnes WHERE kanban = :id ORDER BY orderCol";
        $stmt = this->db->prepare($requete);
        $stmt->execute($data);

        $resultatRequeteC = $stmt->fetchAll();

        $j = 0;
        foreach($resultatRequeteC as $key => $value){
            // Creation d'une colonne
            $requete = "SELECT * FROM taches WHERE idCol = :id";
            $stmt = this->db->prepare($requete);
            $datacol = array(':id' => $value['idCol']);
            $stmt->execute($datacol);

            $resultatRequeteT = $stmt->fetchAll();
            $i = 0;
            foreach($resultatRequeteT as $keyt => $valuet){
                $tasks[$i] = new Task($valuet['descTache'], $valuet['affectation'], $dateLimite['dateLimite']);
                $i++;
            }
            $columns[$j] = new Column($value['nameCol'], $tasks);
            $j++;
            $tasks = null;
        }
        return new Kanban($resultatRequeteK['nameKanban'], $resultatRequeteK['descKanban'], $resultatRequeteK['public'], $resultatRequete['creator'], $membres, $resultatRequete['image'], $columns);
    }

    // Permet de récupérer la liste de tous les objets.
    public function readAll() {
        /*$requete = "SELECT * FROM cheese";
        $resultatRequete = $this->db->query($requete)->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            $a = new Cheese($value['name'], $value['region'], $value['year'], $value['creator']);
            $tabRes[$value['id']] = $a;
        }
        return $tabRes;*/
    }

    // Permet de créer un nouvel objet.
    public function create(Cheese $a) {
        /*$requete = "INSERT INTO cheese (name, region, year, creator) VALUES (:name, :region, :year, :creator) ";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $a->getName(),
            ':region' => $a->getRegion(),
            ':year' => $a->getYear(),
            ':creator' => $a->getCreator()
        );
        $stmt->execute($data);

        $requete = "SELECT MAX(id) FROM cheese";
        return ($this->db->query($requete)->fetch())['MAX(id)'];*/
    }

    // Permet de supprimer un objet de la liste.
    public function delete($id) {
        /*$requete = "DELETE FROM cheese WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $id);
        $stmt->execute($data);*/
    }

    // Permet de modifier un objet.
    public function update($id, $a, $image = null) {
        /*$requete = "UPDATE cheese SET name = :name, region = :region, year = :year WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $a->getName(),
            ':region' => $a->getRegion(),
            ':year' => $a->getYear(),
            ':id' => $id
        );

        $stmt->execute($data);
        if($image != null) {
            $this->addImage($id, $image);
        }*/
    }

    // Permet d'avoir la liste de tous les objets commençant par $search.
    public function research($search) {
        /*$requete = "SELECT * FROM cheese WHERE name like :search";
        $stmt = $this->db->prepare($requete);
        $data = array(':search' => "$search%");
        $stmt->execute($data);

        $resultatRequete = $stmt->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            $a = new Cheese($value['name'], $value['region'], $value['year'], $value['creator']);
            $tabRes[$value['id']] = $a;
        }
        return $tabRes;*/
    }

    // Fonction pour ajouter une image appelée dans la création d'un nouvel fromage ou dans la modification d'un fromage.
    public function addImage($id, $image) {
        /*$requete = "UPDATE cheese SET image = :image WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':image' => '' . $id . '.' . str_replace('image/','',$image['type']),
            ':id' => $id
        );

        $stmt->execute($data);*/
    }
}
