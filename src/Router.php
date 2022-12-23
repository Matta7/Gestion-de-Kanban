<?php

require_once("view/View.php");
require_once("view/PrivateView.php");
require_once("control/Controller.php");

class Router {

    public function main($kanbanDTB, $accountDTB) {

        // Démarre la session avec le nom kanbanID.
        session_name("kanbanID");
        session_start();

        // Création de la vue et du controller (en fonction des tables de données)
        $view = $this->creationView();
        $controller = new Controller($view, $kanbanDTB, $accountDTB);

        // Tableau qui va gérer les permissions.
        $accessTab = $this->creationAccessTab($kanbanDTB);


        // Affiche la liste :
        if(key_exists('liste', $_GET)) {

            // En recherchant.
            if($_GET['liste'] == 'rechercher') {
                $controller->research($_POST);
            }

            // La liste par pages.
            else if($_GET['liste'] > 0) {
                $controller->showList($_GET['liste']);
            }

            // La liste à la page 1.
            else {
                $controller->showList();
            }
        }

        // Affiche certaines actions en fonction des permissions :
        if(key_exists('action', $_GET)) {

            // Page "à propos".
            if($_GET['action'] === 'aPropos' && in_array('aPropos', $accessTab)) {
                $controller->aPropos();
            }

            // Page "Nouveau kanban".
            else if($_GET['action'] === 'nouveau' && in_array('nouveau', $accessTab)) {
                $controller->newKanban();
            }

            // Validation du nouveau kanban.
            else if($_GET['action'] === 'sauverNouveau' && in_array('sauverNouveau', $accessTab)) {
                $controller->saveNewKanban($_POST);
            }

            // Page de connexion.
            else if($_GET['action'] === 'connexion' && in_array('connexion', $accessTab)) {
                $controller->login();
            }

            // Page de validation de la connexion.
            else if($_GET['action'] === 'authentification' && in_array('authentification', $accessTab)) {
                $controller->connected($_POST);
            }

            // Page d'inscription.
            else if($_GET['action'] === 'inscription' && in_array('inscription', $accessTab)) {
                $controller->register();
            }

            // Page de validation de l'inscription.
            else if($_GET['action'] === 'inscrit' && in_array('inscrit', $accessTab)) {
                $controller->registered($_POST);
            }

            // Déconnexion.
            else if($_GET['action'] === 'deconnexion' && in_array('deconnexion', $accessTab)) {
                $controller->disconnection();
            }

            // Affiche en fonction de la liste des kanbans d'autres actions possibles :
            else if(key_exists('id', $_GET) && in_array('id', $accessTab)) {

                // Validation de la supression d'un kanban.
                if ($_GET['action'] === 'supprimerConfirmation'&& in_array('supprimerConfirmation', $accessTab)) {
                    $controller->askKanbanDeletion($_GET['id']);
                }

                // Page de suppression d'un kanban.
                else if ($_GET['action'] === 'supprimer'&& in_array('supprimer', $accessTab)) {
                    $controller->deleteKanban($_GET['id']);
                    $view->makeHomePage();
                }

                // Page de modification d'un kanban.
                else if($_GET['action'] === 'modification'&& in_array('modification', $accessTab)) {
                    $controller->updateKanban($_GET['id']);
                }

                // Page de validation de la modification d'un kanban.
                else if($_GET['action'] === 'sauverModification'&& in_array('sauverModification', $accessTab)) {
                    $controller->updatedKanban($_POST, $_GET['id']);
                }

                // Page d'ajout d'un membre.
                else if($_GET['action'] === 'ajouterMembre'&& in_array('ajouterMembre', $accessTab)) {
                    $controller->addMember($_GET['id']);
                }

                // Page de confirmation d'ajout d'un membre.
                else if($_GET['action'] === 'ajouterMembreConfirmation' && in_array('ajouterMembreConfirmation', $accessTab)) {
                    $controller->addMemberConfirmation($_POST, $_GET['id']);
                }

                // Page de suppression d'un membre. 
                else if($_GET['action'] === 'supprimerMembre' && in_array('supprimerMembre', $accessTab)) {
                    $controller->deleteMember($_GET['id']);
                }

                // Page de confirmation suppression d'un membre. 
                else if($_GET['action'] === 'supprimerMembreConfirmation' && in_array('supprimerMembreConfirmation', $accessTab)) {
                    $controller->deleteMemberConfirmation($_GET['id'], $_GET['login']);
                }

                // Page d'affectation d'une tâche.
                else if($_GET['action'] === 'affectation' && in_array('affectation', $accessTab)) {
                    $controller->affect($_GET);
                }

                // Page de confirmation d'affectation d'une tâche.
                else if($_GET['action'] === 'affectationConfirmation' && in_array('affectationConfirmation', $accessTab)) {
                    $controller->affectConfirmation($_GET);
                }


                // Si aucune des conditions ne sont respectées, alors l'utilisateur n'a pas les permissions.
                else {
                    $this->POSTredirect('index.php', 'Vous n\'avez pas les droits');
                }
            }

            // Si aucune des conditions ne sont respectées, alors l'utilisateur n'a pas les permissions.
            else {
                $this->POSTredirect('index.php', 'Vous n\'avez pas les droits');
            }
        }

        // Si une fonction javascript est exécutée
        if(key_exists('function', $_GET) && in_array('function', $accessTab)) {

            // Si on veut ajouter une tâche
            if($_GET['function'] === 'addTask') {
                echo $controller->addTask($_GET);
                return;
            }

            else if($_GET['function'] === 'dragTasks') {
                $controller->moveTask($_GET);
                return;
            }

            // Si aucune des conditions ne sont respectées, alors l'utilisateur n'a pas les permissions.
            else {
                $this->POSTredirect('index.php', 'Vous n\'avez pas les droits');
            }
        }

        // Affiche les informations d'une page.
        if(key_exists('id', $_GET)) {
            if(in_array('id', $accessTab)) {
                $id = $_GET['id'];
                $controller->showInformation($id);
            }

            else {
                $this->POSTredirect('index.php', 'Vous n\'avez pas les droits');
            }
        }

        // Affiche la page.
        $view->render();
    }


    // Fonction qui créé la vue en fonction de si l'utilisateur est connecté ou non.
    public function creationView() {
        if(key_exists('feedback', $_SESSION)) {
            if(key_exists('user', $_SESSION)) {
                $view = new PrivateView('Page d\'accueil', '<h1> Bienvenue ' . $_SESSION['user']->getName() . ' ! </h1>', $this, $_SESSION['feedback'], $_SESSION['user']);
                unset($_SESSION['feedback']);

            }

            else {
                $view = new View('Page d\'accueil', '<h1> Bienvenue sur le site ! </h1>', $this, $_SESSION['feedback']);
                unset($_SESSION['feedback']);
            }
        }
        else {
            if(key_exists('user', $_SESSION)) {
                $view = new PrivateView('Page d\'accueil', '<h1> Bienvenue ' . $_SESSION['user']->getName() . ' ! </h1>', $this, null, $_SESSION['user']);
                unset($_SESSION['feedback']);
            }

            else {
                $view = new View('Page d\'accueil', '<h1> Bienvenue sur le site ! </h1>', $this, null);
                unset($_SESSION['feedback']);
            }
        }
        return $view;
    }

    // Fonction qui, en fonction de l'utilisateur connecté, va créer un tableau de ses permissions.
    // Cela fonctionne donc avec un système de liste blanche, on interdit de base tout à l'utilisateur et on lui rajoute des droits. 
    public function creationAccessTab($kanbanDTB) {

        if(!key_exists('user',$_SESSION)) {
            return array('aPropos', 'connexion', 'authentification', 'inscription', 'inscrit');
            if(key_exists('id', $_GET)) {
                $kanban = $kanbanDTB->read($_GET['id']);
                if($kanban->isPublic()) {
                    $accessTab = array_merge($accessTab, array('id'));
                }
            }
        }

        else {
            if ($_SESSION['user']->getStatus() === "admin") {
                return array('aPropos', 'deconnexion', 'nouveau', 'sauverNouveau', 'modification', 'sauverModification', 'supprimer', 'supprimerConfirmation', 'ajouterMembre', 'ajouterMembreConfirmation', 'supprimerMembre', 'supprimerMembreConfirmation', 'function', 'affectation', 'affectationConfirmation', 'id');
            }
            else {
                $accessTab = array('aPropos', 'deconnexion', 'nouveau', 'sauverNouveau');
                if(key_exists('id', $_GET)) {
                    $kanban = $kanbanDTB->read($_GET['id']);
                    if($_SESSION['user']->getLogin() === $kanban->getCreator()) {
                        $accessTab = array_merge($accessTab, array('modification', 'sauverModification', 'supprimer', 'supprimerConfirmation', 'ajouterMembre', 'ajouterMembreConfirmation', 'supprimerMembre', 'supprimerMembreConfirmation', 'function', 'affectation', 'affectationConfirmation', 'id'));
                    }
                    else if(in_array($_SESSION['user']->getLogin(), $kanban->getMembers())) {
                        $accessTab = array_merge($accessTab, array('function', 'affectation', 'affectationConfirmation', 'id'));
                    }
                    else if($kanban->isPublic()) {
                        $accessTab = array_merge($accessTab, array('id'));
                    }
                }
                return $accessTab;
            }
        }
    }


    // Les fonctions suivantes retournent l'URL des pages renseignées :
    // Page d'accueil du site.
    public function getHomePageURL() {
        return 'index.php';
    }

    // Page d'un kanban.
    public function getKanbanURL($id) {
        return "?id=$id";
    }

    // Page de création d'un objet.
    public function getKanbanCreationURL() {
        return 'index.php?action=nouveau';
    }

    // Page de validation de création d'un objet.
    public function getKanbanSaveURL() {
        return 'index.php?action=sauverNouveau';
    }

    // Page de validation de supression d'un kanban.
    public function getKanbanAskDeletionURL($id) {
        return "index.php?action=supprimerConfirmation&id=$id";
    }

    // Page de supression d'un kanban.
    public function getKanbanDeletionURL($id) {
        return "index.php?action=supprimer&id=$id";
    }

    // Page de modification d'un kanban.
    public function getKanbanUpdateURL($id){
        return "index.php?action=modification&id=$id";
    }

    // Page de validation de modification d'un kanban.
    public function getKanbanUpdatedURL($id) {
        return "index.php?action=sauverModification&id=$id";
    }

    // Page d'ajout de membre.
    public function getKanbanAddMemberURL($id) {
        return "index.php?action=ajouterMembre&id=$id";
    }
    
    // Page de confirmation d'ajout de membre.
    public function getAddMemberConfirmationURL($id) {
        return "index.php?action=ajouterMembreConfirmation&id=$id";
    }

    // Page de suppression de membre.
    public function getKanbanDeleteMemberURL($id) {
        return "index.php?action=supprimerMembre&id=$id";
    }

    // Page de confirmation de suppression de membre.
    public function getDeleteMemberConfirmationURL($id, $login) {
        return "index.php?action=supprimerMembreConfirmation&id=$id&login=$login";
    }

    // Page pour la fonction de recherche d'un objet.
    public function getKanbanResearchURL() {
        return "index.php?liste=rechercher";
    }

    // Page de connexion.
    public function getLoginURL() {
        return "index.php?action=connexion";
    }

    // Page de validation de connexion.
    public function getAuthenticationURL() {
        return "index.php?action=authentification";
    }

    // Page de déconnexion.
    public function getDisconnectionURL() {
        return "index.php?action=deconnexion";
    }

    // Page "à propos".
    public function getAProposURL(){
        return "index.php?action=aPropos";
    }

    // Page d'inscription.
    public function getRegistrationURL() {
        return "index.php?action=inscription";
    }

    // Page de validation d'inscription.
    public function getRegisteredURL() {
        return "index.php?action=inscrit";
    }

    // Fonction de pagination de la liste des objets.
    public function getPageURL($page) {
        return "index.php?liste=$page";
    }

    public function getAffectationURL($id, $taskId) {
        return "index.php?action=affectation&id=$id&idTache=$taskId";
    }

    public function getAffectationConfirmationURL($id, $taskId, $login) {
        return "index.php?action=affectationConfirmation&id=$id&idTache=$taskId&login=$login";
    }

    
    public function POSTredirect($url, $feedback) {
        $_SESSION['feedback'] = $feedback;
        header("Location: ".htmlspecialchars_decode($url), true, 303);
        die;
    }
}
