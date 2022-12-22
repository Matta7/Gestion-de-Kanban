<?php

require_once('model/KanbanBuilder.php');
require_once('model/Kanban.php');
require_once('model/Column.php');
require_once('model/KanbanStorage.php');
require_once('model/Task.php');

class KanbanStorageMySQL /*implements KanbanStorage*/ {

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
        $stmt = $this->db->prepare($requete);
        $stmt->execute($data);

        $resultatRequeteM = $stmt->fetchAll();
        $i = 0;
        foreach($resultatRequeteM as $key => $value){
            $membres[$i] = $value['login'];
            $i++;
        }

        // Créations des colonnes
        $requete = "SELECT * FROM colonnes WHERE kanban = :id ORDER BY orderCol";
        $stmt = $this->db->prepare($requete);
        $stmt->execute($data);

        $resultatRequeteC = $stmt->fetchAll();

        $j = 0;
        foreach($resultatRequeteC as $key => $value){
            // Creation d'une colonne
            $requete = "SELECT * FROM taches WHERE idCol = :id";
            $stmt = $this->db->prepare($requete);
            $datacol = array(':id' => $value['idCol']);
            $stmt->execute($datacol);

            $resultatRequeteT = $stmt->fetchAll();
            $i = 0;
            foreach($resultatRequeteT as $keyt => $valuet){
                $tasks[$i] = new Task($valuet['idTache'], $valuet['descTache'], $valuet['affectation'], $valuet['dateLimite']);
                $i++;
            }
            $columns[$j] = new Column($value['idCol'], $value['nameCol'], $tasks);
            $j++;
            $tasks = null;
        }
        return new Kanban($resultatRequeteK['nameKanban'], $resultatRequeteK['descKanban'], $resultatRequeteK['public'], $resultatRequeteK['creator'], $membres, $resultatRequeteK['image'], $columns);
    }

    // Permet de récupérer la liste de tous les objets.
    public function readAll() {
        $requete = "SELECT * FROM kanban";
        $resultatRequete = $this->db->query($requete)->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            // On prend aussi les membres
            $requete = "SELECT * FROM membres WHERE idKanban = :id";
            $stmt = $this->db->prepare($requete);
            $data = array(':id' => $value['idKanban']);
            $stmt->execute($data);

            $resultatRequeteM = $stmt->fetchAll();
            $membres = null;
            $i = 0;
            foreach($resultatRequeteM as $keyM => $valueM){
                $membres[$i] = $valueM['login'];
                $i++;
            }

            $k = new Kanban($value['nameKanban'], $value['descKanban'], $value['public'], $value['creator'], $membres);
            $tabRes[$value['idKanban']] = $k;
        }
        return $tabRes;
    }

    // Permet d'inserer le kanban données dans la base de données. 
    public function create($k) {
        $requete = "INSERT INTO kanban (nameKanban, descKanban, creator, public) VALUES (:name, :desc, :creator, :public)";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $k->getName(),
            ':desc' => $k->getDesc(),
            ':creator' => $k->getCreator(),
            ':public' => (-1+($k->isPublic()))
        );
        $stmt->execute($data);

        $requete = "SELECT MAX(idKanban) FROM kanban";
        $resId = ($this->db->query($requete)->fetch())['MAX(idKanban)'];
        
        // On met les colonnes par défauts
        $requete = "INSERT INTO colonnes(nameCol, orderCol, kanban) VALUES 
            ('Stories', 0, :id), 
            ('Terminées', 1, :id)";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $resId);
        $stmt->execute($data);

        return $resId;
    }

    // Permet de supprimer un Kanban partir de son id.
    public function delete($id) {
        $requete = "DELETE FROM kanban WHERE idKanban = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $id);
        $stmt->execute($data);
    }

    // Permet de modifier les informations d'un kanban.
    public function updateKanbanInfo($id, $k, $image) {
        $requete = "UPDATE kanban SET nameKanban = :name, descKanban = :desc, public = :public WHERE idKanban = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $k->getName(),
            ':desc' => $k->getDesc(),
            ':id' => $id
        );
        if($k->isPublic()) {
            $data[':public'] = 0;
        }
        else {
            $data[':public'] = 1;
        }

        $stmt->execute($data);
    }
    // Insère la colonne (vide) $col a la position $pos dans le kanban d'id $id 
    public function addColumn($id, $pos, $colName){
        // On bouge tout apres en avant pour faire de la place
        $requete = "UPDATE colonnes SET orderCol = orderCol+1 WHERE kanban = :id AND orderCol >= :pos ";
        $stmt = $this->db->prepare($requete);
        $data = array(':pos' => $pos,
            ':id' => $id
        );

        $stmt->execute($data);

        $requete = "INSERT INTO colonnes(nameCol, orderCol, kanban) VALUES (:name, :pos, :id)";
        $stmt = $this->db->prepare($requete);
        $data = array(':pos' => $pos,
            ':id' => $id,
            ':name' => $colName
        );

        $stmt->execute($data);

        $requete = "SELECT MAX(idCol) FROM colonnes";
        return $this->db->query($requete)->fetch()['MAX(idCol)'];
    }

    // Requête pour ajouter une tâche.
    public function addTask($idCol, $descTache) {
        // On bouge tout apres en avant pour faire de la place
        $requete = "INSERT INTO taches (idCol, descTache, affectation, dateLimite) VALUES (:idCol, :descTache, NULL, NULL)";
        $stmt = $this->db->prepare($requete);
        $data = array(':idCol' => $idCol,
            ':descTache' => $descTache
        );
        $stmt->execute($data);

        $requete = "SELECT MAX(idTache) FROM taches";
        return $this->db->query($requete)->fetch()['MAX(idTache)'];
    }

    // Fonction pour bouger une tâche d'une colonne à une autre.
    public function moveTask($idCol, $idTache) {
        $requeteAlterTask = "UPDATE taches t SET idCol = :colId WHERE idTache = :id";
        $stmt = $this->db->prepare($requeteAlterTask);
        $data = array(':colId' => $idCol, ':id' => $idTache);
        $stmt->execute($data);
    }

    // Fonction pour supprimer une tâche.
    public function deleteTask($idTache) {
        $requete = "DELETE FROM taches WHERE idTache = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $idTache);
        $stmt->execute($data);
    }


    // Permet d'avoir la liste de tous les objets commençant par $search.
    public function research($search) {
        $requete = "SELECT * FROM kanban WHERE nameKanban like :search";
        $stmt = $this->db->prepare($requete);
        $data = array(':search' => "$search%");
        $stmt->execute($data);

        $resultatRequete = $stmt->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            // On prend aussi les membres
            $requete = "SELECT * FROM membres WHERE idKanban = :id";
            $stmt = $this->db->prepare($requete);
            $data = array(':id' => $value['idKanban']);
            $stmt->execute($data);

            $resultatRequeteM = $stmt->fetchAll();
            $membres = null;
            $i = 0;
            foreach($resultatRequeteM as $keyM => $valueM){
                $membres[$i] = $valueM['login'];
                $i++;
            }

            $k = new Kanban($value['nameKanban'], $value['descKanban'], $value['public'], $value['creator'], $membres);
            $tabRes[$value['idKanban']] = $k;
        }
        return $tabRes;
    }

    // Fonction pour ajouter une image.
    public function addImage($id, $image) {
        /*$requete = "UPDATE kanban SET image = :image WHERE kanban . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':image' => '' . $id . '.' . str_replace('image/','',$image['type']),
            ':id' => $id
        );

        $stmt->execute($data);*/
    }
}
