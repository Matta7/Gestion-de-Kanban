<?php

require_once('view/View.php');
require_once("view/PrivateView.php");
require_once('model/Kanban.php');
require_once('model/KanbanBuilder.php');
require_once('control/AuthenticationManager.php');

/*
Le controller va faire intéragir le model et la vue.
*/

class Controller {

    private $view;
    private $kanbanDTB;
    private $authenticationManager;

    public function __construct($view, $kanbanDTB, $accountDTB) {
        $this->view = $view;
        $this->kanbanDTB = $kanbanDTB;
        $this->authenticationManager = new AuthenticationManager($accountDTB);
    }

    // Si l'action est "aPropos".
    public function aPropos() {
        $this->view->makeAProposPage();
    }

    // Affiche l'information d'un kanban. Si le kanban indiqué n'existe pas, alors cela affiche une page d'erreur.
    public function showInformation($id) {
        if(!key_exists($id, $this->kanbanDTB->readAll())) {
            $this->view->makeUnknownKanbanPage();
        }
        else {
            $this->view->makeKanbanPage($this->kanbanDTB->read($id), $id);
        }
    }

    // Affiche la liste des kanbans, par défaut la page une.
    // Enlève l'index 'search' (pour la fonction de recherche) de la session.
    // Enlève l'index 'currentUpdateKanban' (modification d'un kanban) de la session.
    public function showList($page = 1) {
        unset($_SESSION['search']);
        unset($_SESSION['currentUpdateKanban']);

        $this->view->makeListPage($this->kanbanDTB->readAll(), $page);
    }

    // Page de création d'un nouveau kanban.
    // Vérifie si l'utilisateur a déjà rentré un nouveau kanban sans le valider (dans la session).
    public function newKanban() {
        if(key_exists('currentNewKanban', $_SESSION)) {
            $this->view->makeKanbanCreationPage($_SESSION['currentNewKanban']);
        }
        else {
            $this->view->makeKanbanCreationPage();
        }
    }

    // Permet de sauvegarder un nouvel objet créé par un utilisateur.
    public function saveNewKanban(array $data) {
        if($data['public'] === "on") {
            $data['public'] = 0;
        }
        else {
            $data['public'] = 1;
        }
        $kanbanBuilder = new KanbanBuilder($data);

        // Si le kanban entré par l'utilisateur est valide, alors on supprime la sauvegarde actuelle et on créer le kanban.
        if($kanbanBuilder->isValid()) {
            $error = false;
            unset($_SESSION['currentNewKanban']);
            $k = $kanbanBuilder->createKanban();

            $id = $this->kanbanDTB->create($k);

            // Si une image a été envoyée avec, on vérifie que celle-ci est valide, on la stocke dans le dossier upload et on l'ajoute à la base de données.
            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($kanbanBuilder->isImageValid($_FILES['image'], $id)) {

                        if(file_exists("upload/$id." . str_replace('image/', '', $_FILES['image']['type']))) {
                            unlink('upload/' . "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        $this->kanbanDTB->addImage($id, $_FILES['image']);
                    }

                    // Si l'image a une erreur, on supprime l'objet créé.
                    else {
                        $error = true;
                        $this->kanbanDTB->delete($id);
                        $_SESSION['currentNewKanban'] = $kanbanBuilder;
                        $this->view->displayKanbanCreationFailure();
                    }
                }
            }

            // S'il n'y a pas d'erreur (avec l'image), on affiche la page que l'on vient de créer.
            if(!$error) {
                $this->view->displayKanbanCreationSuccess($id);
            }
        }

        // S'il y a une erreur, on sauvegarde les données que l'utilisateur a entré et on affiche une page d'erreur.
        else {
            $_SESSION['currentNewKanban'] = $kanbanBuilder;
            $this->view->displayKanbanCreationFailure();
        }
    }

    // Affiche une page une page de confirmation de suppression d'une page.
    public function askKanbanDeletion($id) {
        if($this->kanbanDTB->read($id) != null) {
            $this->view->makeKanbanDeletionPage($id);
        }

        else {
            $this->view->makeUnknownKanbanPage();
        }
    }

    // Supprime la page après avoir confirmé la suppression.
    public function deleteKanban($id) {
        if($this->kanbanDTB->read($id) != null) {
            $this->kanbanDTB->delete($id);
            $this->view->displayKanbanDeletionSuccess();
        }
        else {
            $this->view->displayKanbanDeletionFailure();
        }
    }

    // Page de modification d'un kanban.
    // Si l'on était déjà en train de modifié un kanban, cela est enregistré dans la session.
    public function updateKanban($id) {
        // Ouvre la page de modification avec les champs de la la session.
        if(key_exists('currentUpdateKanban', $_SESSION)) {
            $this->view->makeKanbanUpdatePage($id, $_SESSION['currentUpdateKanban']);
        }

        // Ouvre la page de modification avec les champs de base.
        else {
            $k = $this->kanbanDTB->read($id);
            if($k->isPublic() === true) {
                $public = 0;
            }
            else {
                $public = 1;
            }
            $data = array('name' => $k->getName(), 'desc' => $k->getDesc(), 'public' => $public);
            $this->view->makeKanbanUpdatePage($id, new KanbanBuilder($data));
        }
    }

    // Fonction qui envoie la modification. Ce sont les mêmes vérifications que la création d'un nouveau kanban.
    public function updatedKanban(array $data, $id) {
        if($data['public'] === "on") {
            $data['public'] = 0;
        }
        else {
            $data['public'] = 1;
        }
        $kanbanBuilder = new KanbanBuilder($data);

        $image = null;
        $error = false;
        if($kanbanBuilder->isValid()) {

            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($kanbanBuilder->isImageValid($_FILES['image'], $id)) {
                        $oldImage = $this->kanbanDTB->read($id)->getImage();
                        if($oldImage != null) {
                            unlink('upload/' . $oldImage);
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                    }
                    else {
                        $error = true;
                        $_SESSION['currentUpdateKanban'] = $kanbanBuilder;
                        $this->view->displayKanbanUpdatedFailure($id);
                    }
                }
            }

            if(!$error) {
                var_export($kanbanBuilder->createKanban());
                unset($_SESSION['currentUpdateKanban']);
                $this->kanbanDTB->updateKanbanInfo($id, $kanbanBuilder->createKanban(), $image);
                $this->view->displayKanbanUpdatedSuccess($id);
            }
        }

        else {
            $_SESSION['currentUpdateKanban'] = $kanbanBuilder;
            $this->view->displayKanbanUpdatedFailure($id);
        }
    }

    // Ajouter une tâche.
    public function addTask($data) {
        if($data['descTache'] === strip_tags($data['descTache'])) {
            return $this->kanbanDTB->addTask($data['idCol'], $data['descTache']);
        }

        else {
            $this->view->displayAddTaskFailure($data['id']);
        }
    }

    // Bouger une tâche d'une colonne à une autre.
    public function moveTask($data) {
        $this->kanbanDTB->moveTask($data['idCol'], $data['idTache']);
    }



    // Fonction pour ajouter un membre.
    public function addMember($id) {
        $this->view->makeAddMemberPage($id);
    }

    // Fonction pour la confirmation de l'ajout d'un membre.
    public function addMemberConfirmation($data, $id) {
        $a = $this->authenticationManager->getAccount($data['login']);
        $k = $this->kanbanDTB->read($id);
        $members = $k->getMembers();
        if($a->getLogin() === NULL || in_array($data['login'], $members) || $data['login'] === $k->getCreator()) {
            $this->view->displayAddMemberConfirmationFailure($id);
        }
        else {
            $this->kanbanDTB->addMember($id, $data['login']);
            $this->view->displayAddMemberConfirmationSuccess($id);
        }
    }


    public function deleteMember($id) {
        $this->view->makeDeleteMemberPage($id);
    }

    public function deleteMemberConfirmation($id) {
        $this->kanbanDTB->removeMember($id, $login);
    }


    // Fonction pour la recherche dans la liste de Kanban.
    public function research($data) {
        unset($_SESSION['search']);
        if($data['search'] === strip_tags($data['search']) && $data['search'] !== '') {
            $_SESSION['search'] = $data['search'];
            $this->view->makeListPage($this->kanbanDTB->research($data['search']));
        }

        else {
            $this->view->displayKanbanResearchListFailure();
        }
    }

    // Affiche la page de connexion.
    public function login() {
        $this->view->makeLoginFormPage();
    }

    // Fonction qui gère l'utilisateur qui se connecte.
    public function connected($data) {
        if($this->authenticationManager->isUserConnected()) {
            $this->authenticationManager->disconnectUser();
        }

        $this->authenticationManager->connectUser($data['login'], $data['password']);
        if($this->authenticationManager->isUserConnected()) {
            $this->view->displayKanbanAuthenticationSuccess($this->authenticationManager->getUserName());
        }

        else {
            $this->view->displayKanbanAuthenticationFailure();
        }
    }

    // Fonction qui gère la déconnexion.
    public function disconnection() {
        $this->authenticationManager->disconnectUser();
        $this->view->displayKanbanDisconnectionFailure();
    }

    // Fonction qui affiche la page d'inscription.
    public function register() {
        $this->view->makeRegistrationFormPage();
    }

    // Fonction qui gère l'inscription.
    public function registered($data) {
        if($data['password'] === $data['confirmPassword']) {
            if($this->authenticationManager->registration($data['name'], $data['login'], $data['password'])) {
                $this->authenticationManager->connectUser($data['login'], $data['password']);
                $this->view->displayKanbanRegistrationSuccess($data['name']);
            }
            else {
                $this->view->displayKanbanRegistrationFailure();
            }
        }
        else {
            $this->view->displayKanbanRegistrationFailure();
        }
    }
}
