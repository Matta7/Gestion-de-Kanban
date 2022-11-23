<?php

require_once('view/View.php');
require_once('model/Cheese.php');
require_once('model/CheeseBuilder.php');
require_once('control/AuthenticationManager.php');

/*
Le controller va faire intéragir le model et la vue.
*/

class Controller {

    private $view;
    private $cheeseTab;
    private $authenticationManager;

    public function __construct($view, $cheeseTab, $accountTab) {
        $this->view = $view;
        $this->cheeseTab = $cheeseTab;
        $this->authenticationManager = new AuthenticationManager($accountTab);
    }

    // Si l'action est "aPropos".
    public function aPropos() {
        $this->view->makeAProposPage();
    }

    // Affiche l'information d'un fromage. Si le fromage indiqué n'existe pas, alors cela affiche une page d'erreur.
    public function showInformation($id) {
        if(!key_exists($id, $this->cheeseTab->readAll())) {
            $this->view->makeUnknownCheesePage();
        }
        else {
            $this->view->makeCheesePage($this->cheeseTab->read($id), $id);
        }
    }

    // Affiche la liste des fromages, par défaut la page une.
    // Enlève l'index 'search' (pour la fonction de recherche) de la session.
    // Enlève l'index 'currentUpdateCheese' (modification d'un fromage) de la session.
    public function showList($page = 1) {
        unset($_SESSION['search']);
        unset($_SESSION['currentUpdateCheese']);

        $this->view->makeListPage($this->cheeseTab->readAll(), $page);
    }

    // Page de création d'un fromage.
    // Vérifie si l'utilisateur a déjà rentré un nouveau fromage sans le valider (dans la session).
    public function newCheese() {
        if(key_exists('currentNewCheese', $_SESSION)) {
            $this->view->makeCheeseCreationPage($_SESSION['currentNewCheese']);
        }
        else {
            $this->view->makeCheeseCreationPage();
        }
    }

    // Permet de sauvegarder un nouvel objet créé par un utilisateur.
    public function saveNewCheese(array $data) {
        $cheeseBuilder = new cheeseBuilder($data);

        // Si le fromage entré par l'utilisateur est valide, alors on supprime la sauvegarde actuelle et on créer le fromage.
        if($cheeseBuilder->isValid()) {
            $error = false;
            unset($_SESSION['currentNewCheese']);
            $a = $cheeseBuilder->createCheese();

            $id = $this->cheeseTab->create($a);

            // Si une image a été envoyée avec, on vérifie que celle-ci est valide, on la stocke dans le dossier upload et on l'ajoute à la base de données.
            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($cheeseBuilder->isImageValid($_FILES['image'], $id)) {

                        if(file_exists("upload/$id." . str_replace('image/', '', $_FILES['image']['type']))) {
                            unlink('upload/' . "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        $this->cheeseTab->addImage($id, $_FILES['image']);
                    }

                    // Si l'image a une erreur, on supprime l'objet créé.
                    else {
                        $error = true;
                        $this->cheeseTab->delete($id);
                        $_SESSION['currentNewCheese'] = $cheeseBuilder;
                        $this->view->displayCheeseCreationFailure();
                    }
                }
            }

            // S'il n'y a pas d'erreur (avec l'image), on affiche la page que l'on vient de créer.
            if(!$error) {
                $this->view->displayCheeseCreationSuccess($id);
            }
        }

        // S'il y a une erreur, on sauvegarde les données que l'utilisateur a entré et on affiche une page d'erreur.
        else {
            $_SESSION['currentNewCheese'] = $cheeseBuilder;
            $this->view->displayCheeseCreationFailure();
        }
    }

    // Affiche une page une page de confirmation de suppression d'une page.
    public function askCheeseDeletion($id) {
        if($this->cheeseTab->read($id) != null) {
            $this->view->makeCheeseDeletionPage($id);
        }

        else {
            $this->view->makeUnknownCheesePage();
        }
    }

    // Supprime la page après avoir confirmé la suppression.
    public function deleteCheese($id) {
        if($this->cheeseTab->read($id) != null) {
            $this->cheeseTab->delete($id);
            $this->view->displayCheeseDeletionSuccess();
        }
        else {
            $this->view->displayCheeseDeletionFailure();
        }
    }

    // Page de modification d'un fromage.
    // Si l'on était déjà en train de modifié un fromage, cela est enregistré dans la session.
    public function updateCheese($id) {
        // Ouvre la page de modification avec les champs de la la session.
        if(key_exists('currentUpdateCheese', $_SESSION)) {
            $this->view->makeCheeseUpdatePage($id, $_SESSION['currentUpdateCheese']);
        }

        // Ouvre la page de modification avec les champs de base.
        else {
            $a = $this->cheeseTab->read($id);
            $data = array('name' => $a->getName(), 'region' => $a->getRegion(), 'year' => $a->getYear());
            $this->view->makeCheeseUpdatePage($id, new CheeseBuilder($data));
        }
    }

    // Fonction qui envoie la modification. Ce sont les mêmes vérifications que la création d'un nouveau fromage.
    public function updatedCheese(array $data, $id) {
        $cheeseBuilder = new CheeseBuilder($data);
        $image = null;
        $error = false;
        if($cheeseBuilder->isValid()) {

            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($cheeseBuilder->isImageValid($_FILES['image'], $id)) {
                        $oldImage = $this->cheeseTab->read($id)->getImage();
                        if($oldImage != null) {
                            unlink('upload/' . $oldImage);
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                    }
                    else {
                        $error = true;
                        $_SESSION['currentUpdateCheese'] = $cheeseBuilder;
                        $this->view->displayCheeseUpdatedFailure($id);
                    }
                }
            }

            if(!$error) {
                unset($_SESSION['currentUpdateCheese']);
                $this->cheeseTab->update($id, $cheeseBuilder->createCheese(), $image);
                $this->view->displayCheeseUpdatedSuccess($id);
            }
        }

        else {
            $_SESSION['currentUpdateCheese'] = $cheeseBuilder;
            $this->view->displayCheeseUpdatedFailure($id);
        }
    }

    // Fonction pour la recherche d'objet.
    public function research($data) {
        unset($_SESSION['search']);
        if($data['search'] === strip_tags($data['search']) && $data['search'] !== '') {
            $_SESSION['search'] = $data['search'];
            $this->view->makeListPage($this->cheeseTab->research($data['search']));
        }

        else {
            $this->view->displayCheeseResearchListFailure();
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
            $this->view->displayCheeseAuthenticationSuccess($this->authenticationManager->getUserName());
        }

        else {
            $this->view->displayCheeseAuthenticationFailure();
        }
    }

    // Fonction qui gère la déconnexion.
    public function disconnection() {
        $this->authenticationManager->disconnectUser();
        $this->view->displayCheeseDisconnectionFailure();
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
                $this->view->displayCheeseRegistrationSuccess($data['name']);
            }
            else {
                $this->view->displayCheeseRegistrationFailure();
            }
        }
        else {
            $this->view->displayCheeseRegistrationFailure();
        }
    }
}
