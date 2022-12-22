<?php

class View {

    protected $title;
    protected $content;
    protected $router;
    protected $feedback;

    public function __construct($title, $content, $router, $feedback) {
        $this->title = $title; // Titre de la page
        $this->content = $content; // Contenu de <body>
        $this->router = $router; // URL
        $this->feedback = $feedback; // Message d'erreur
    }

    // Fonction pour afficher tout le contenu de la page.
    public function render() {
        if($this->title === null || $this->content === null) {
            $this->makeTestPage();
        }
        $title = $this->title;
        $content = $this->content;
        $menu = $this->getMenu();
        $feedback = $this->feedback;

        include("template.php");
    }

    // Fonction qui va afficher le menu pour accéder aux différentes fonctionnalités.
    public function getMenu() {
        $menu = "<nav><ul><li id = 'accueil'> <a href='index.php'>Page d'accueil</a></li>\n";
        $menu .= "<li id='listeKanbans'><a href='?liste'>Liste des kanbans</a></li>\n";
        $menu .= "<li><a id='aPropos' href='?action=" . 'aPropos' . "'>À Propos</a></li></ul></nav>";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getLoginURL() . "';\">Se connecter</button>\n";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getRegistrationURL() . "';\">S'inscrire</button>\n";

        return $menu;
    }

    // Page d'accueil.
    public function makeHomePage() {
        $this->title = 'Page d\'accueil';
        $this->content = '<h1> Bienvenue sur le site ! </h1>\n';
    }

    // Page de debug.
    public function makeTestPage() {
        $this->title = "Test";
        $this->content = "Test\n";
    }

    // Page d'erreur.
    public function makeUnknownKanbanPage() {
        $this->title = "Kanban inconnu";
        $this->content = "Kanban inconnu\n";
    }

    // Page affichant l'objet kanban.
    public function makeKanbanPage($kanban, $id = null) {
        $this->title = $kanban->getName();
        $this->content = "<h1>" . $kanban->getName() . "</h1>\n" . '<p>' . $kanban->getDesc() . "</p>\n<p> Créateur du kanban : " . $kanban->getCreator() . "</p>\n";
        
        $this->content .= "<p> Membres : ";
        $members = $kanban->getMembers();
        for($i=0; $i < count($members); $i++) {
            if($i != 0) {
                $this->content .= ", ";
            }
            $this->content .= $members[$i];
        }
        $this->content .= ".</p>\n";
        /*$this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanAddMemberURL($id) . "';\">Ajouter un membre</button>\n";
        $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanDeleteMemberURL($id) . "';\">Supprimer un membre</button>\n";*/

        $this->content .= "<div class=\"kanban\" id=\"kanban-" . $id . "\">\n";
        foreach($kanban->getColumns() as $c) {
            $this->content .= "<div id=\"colonne-" . $c->getId() . "\" class=\"colonne\">\n";
            $this->content .= "<h2 class=\"nom-colonne\">" . $c->getName() . "</h2>\n";
            foreach($c->getTasks() as $t) {
                $this->content .= "<div id=\"tache-" . $t->getId() . "\" class=\"tache\" draggable=\"true\">" . $t->getDesc() . "</div>\n";
            }
            $this->content .= "</div>\n";
        }
        $this->content .= "</div>\n";

        //$this->content .= "<a href='" .  ."'> Modifier </a>\n";
        //$this->content .= "<a href='" . $this->router->getKanbanAskDeletionURL($id) ."'> Supprimer </a>\n";
        if($kanban->getImage() != null) {
            $this->content .= "<p><img src='upload/" . $kanban->getImage() . "' alt='" . $kanban->getName() . "'></p>\n";
        }
    }

    // Page affichant la liste, avec par défaut la page une.
    public function makeListPage($kanbanTab, $page = 1) {
        $this->title = 'Liste des kanbans';

        $this->content = "<form action='" . $this->router->getKanbanResearchURL() . "' method='POST'>\n";
        if(key_exists('search', $_SESSION)) {
            $this->content .= "<p>Rechercher : <input type='text' name='search' value='" . $_SESSION['search'] . "'/></p></form>\n";
        }
        else {
            $this->content .= "<p>Rechercher : <input type='text' name='search'/></p></form>\n";
        }

        if(key_exists('search', $_SESSION)) {
            $this->content .= "<nav>\n<ul>\n";
            foreach($kanbanTab as $key => $value) {
                $this->content .= "<li><a href='" . $this->router->getKanbanURL($key) . "'>" . $value->getName() . "</a></li>\n";
            }
            $this->content .= "</ul>\n</nav>\n";
        }
        else {
            $this->pagination($kanbanTab, $page);
        }
    }

    // Fonction pour la pagination. Il y a ici 5 objets par pages.
    public function pagination($kanbanTab, $page) {

        $nbObjectPerPage = 5;
        $pagination = array_keys($kanbanTab);
        $nbObject = count($pagination);
        $nbPages = ceil($nbObject / $nbObjectPerPage)+1;
        $firstObjet = ($page * $nbObjectPerPage) - $nbObjectPerPage;

        $error = false;
        if($page >= $nbPages) {
            $this->makeListPage($kanbanTab, $page-1);
            $error = true;
        }

        if(!$error) {
            $this->content .= "<nav>\n<ul>\n";
            for ($i = $firstObjet; $i < $firstObjet + $nbObjectPerPage; $i++) {
                if(key_exists($i, $pagination)) {
                    $this->content .= "<li><a href='" . $this->router->getKanbanURL($pagination[$i]) . "'>" . $kanbanTab[$pagination[$i]]->getName() . "</a></li>\n";
                }
            }
            $this->content .= "</ul>\n</nav>\n";

            for ($i = 1; $i < $nbPages; $i++) {
                $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getPageURL($i) . "';\">$i</button>\n";
            }
        }
    }

    // Page de debug.
    public function makeDebugPage($variable) {
        $this->title = 'Debug';
        $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)) . "</pre>\n";
    }

    // Page de création de kanban.
    public function makeKanbanCreationPage($kanbanBuilder = null) {
        if ($kanbanBuilder === null) {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanSaveURL() . "' method='POST'>\n
            <p>Nom du Kanban : <input type='text' name='name' /></p>\n
            <p>Description : <input type='text' name='desc' /></p>\n
            <p>Public : <input type='checkbox' name='public' /></p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'></p>\n
            <p><input type='submit' value='Créer'></p>\n
            </form>\n";
        } else {
            $kanban = $kanbanBuilder->createKanban();
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanSaveURL() . "' method='POST'>\n
            <p>Nom du Kanban : <input type='text' name='name' value='" . $kanban->getName() . "' />" . $kanbanBuilder->getError()['name'] . "</p>\n
            <p>Description : <input type='text' name='desc' value='" . $kanban->getDesc() . "' />" . $kanbanBuilder->getError()['region'] . "</p>\n";
            if($kanban->isPublic()) {
                $this->content .= "<p>Public : <input type='checkbox' name='public' checked /></p>\n";
            }
            else {
                $this->content .= "<p>Public : <input type='checkbox' name='public'/></p>\n";
            }
            $this->content .= "<p>Insérer une image de fond (optionnel) : <input type='file' name='image'>" . $kanbanBuilder->getError()['image'] . "</p>\n
            <p><input type='submit' value='Créer'></p>\n
            </form>\n";
        }
    }

    // Page de suppression d'objet.
    public function makeKanbanDeletionPage($id) {
        $this->title = 'Supprimer ?';
        $this->content = "<h2>Voulez vous vraiment supprimer ce kanban ?</h2>\n<form action='".$this->router->getKanbanDeletionURL($id)."' method='POST'>\n
        <button>Supprimer</button>\n
        </form>\n";
    }

    // Page de modification d'objet.
    public function makeKanbanUpdatePage($id, $kanbanBuilder = null) {
        $kanban = $kanbanBuilder->createKanban();
        if($kanbanBuilder->getError() != null) {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du Kanban : <input type='text' name='name' value='" . $kanban->getName() . "' />" . $kanbanBuilder->getError()['name'] . "</p>\n
            <p>Description : <input type='text' name='desc' value='" . $kanban->getDesc() . "' />" . $kanbanBuilder->getError()['desc'] . "</p>\n";
            if($kanban->isPublic()) {
                $this->content .= "<p>Public : <input type='checkbox' name='public' checked /></p>\n";
            }
            else {
                $this->content .= "<p>Public : <input type='checkbox' name='public'/></p>\n";
            }            
            $this->content .= "<p>Insérer une image correspondant (optionnel) : <input type='file' name='image'>" . $kanbanBuilder->getError()['image'] . "</p>\n
            <p><input type='submit' value='Modifier'></p>\n
            </form>\n";
        }

        else {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du Kanban : <input type='text' name='name' value='" . $kanban->getName() . "' /></p>\n
            <p>Description : <input type='text' name='desc' value='" . $kanban->getDesc() . "' /></p>\n";
            if($kanban->isPublic()) {
                $this->content .= "<p>Public : <input type='checkbox' name='public' checked /></p>\n";
            }
            else {
                $this->content .= "<p>Public : <input type='checkbox' name='public'/></p>\n";
            }
            $this->content .= "<p>Insérer une image correspondant (optionnel) : <input type='file' name='image'></p>\n
            <p><input type='submit' value='Modifier'></p>\n
            </form>\n";
        }

    }

    // Page de connexion.
    public function makeLoginFormPage() {
        $this->title = 'Connexion';
        $this->content = "<form action='" . $this->router->getAuthenticationURL() . "' method='POST'>\n
        <p>Nom d'utilisateur : <input type='text' name='login'/></p>\n
        <p>Mot de passe : <input type='password' name='password'/></p>\n
        <p><input type='submit' value='Se connecter'></p>\n
        </form>\n";
    }

    // Page d'inscription.
    public function makeRegistrationFormPage() {
        $this->title = 'Inscription';
        $this->content = "<form action='" . $this->router->getRegisteredURL() . "' method='POST'>\n
        <p>Nom : <input type='text' name='name'/></p>\n
        <p>Nom d'utilisateur : <input type='text' name='login'/></p>\n
        <p>Mot de passe : <input type='password' name='password'/></p>\n
        <p>Confirmer mot de passe : <input type='password' name='confirmPassword'/></p>\n
        <p><input type='submit' value='Se connecter'></p>\n
        </form>\n";
    }

    // Page d'ajout de membre.
    public function makeAddMemberPage($id) {
        $this->title = 'Ajout de membre';
        $this->content = "<form action='" . $this->router->getAddMemberConfirmationURL($id) . "' method='POST'>\n
        <p>Nom d'utilisateur du membre à ajouter : <input type='text' name='login'/></p>\n
        <p><input type='submit' value='Ajouter un membre'></p>\n
        </form>\n";
    }

    // Page de suppression d'un membre.
    public function makeDeleteMemberPage($id, $members) {
        $this->title = 'Suppression de membre';
        $this->content = "";
        for($i=0; $i < count($members); $i++) {
            $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getDeleteMemberConfirmationURL($id, $members[$i]) . "'\">Supprimer " . $members[$i] . "</button>\n";
        }
    }

    // Page "à propos".
    public function makeAProposPage(){
        $this->title = 'À propos';
    }

    // Redirection vers une autre page après avoir créé un objet.
    public function displayKanbanCreationSuccess($id){
        $this->router->POSTredirect('?id='. $id, "Le kanban a été crée avec succès.");
    }

    // Redirection vers une autre page après avoir créé un objet avec une erreur.
    public function displayKanbanCreationFailure() {
        $this->router->POSTredirect('?action=nouveau', "Un champ est invalide.");
    }

    // Redirection vers une autre page après avoir supprimé un objet.
    public function displayKanbanDeletionSuccess() {
        $this->router->POSTredirect('?liste', "Le kanban a été supprimé avec succès.");
    }

    // Redirection vers une autre page après avoir supprimé un objet avec une erreur.
    public function displayKanbanDeletionFailure($id) {
        $this->router->POSTredirect("?id=$id", "L'action a échoué.");
    }

    // Redirection vers une autre page après avoir créé un objet.
    public function displayKanbanUpdatedSuccess($id) {
        $this->router->POSTredirect("?id=$id", "Le kanban a été modifié.");
    }

    public function displayKanbanUpdatedFailure($id) {
        $this->router->POSTredirect("?action=modification&id=$id", "Un champ est invalide.");
    }

    // Redirection vers une autre page après avoir recherché un objet.
    public function displayKanbanResearchListSuccess($search) {
        $this->router->POSTredirect("?liste=$search", "Kanbans commençant par $search :");
    }

    // Redirection vers une autre page après avoir recherché un objet avec une erreur.
    public function displayKanbanResearchListFailure() {
        $this->router->POSTredirect("?liste", "Recherche invalide");
    }

    // Redirection vers une autre page après s'être connecté.
    public function displayKanbanAuthenticationSuccess($name) {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Vous êtes connecté $name.");
    }

    // Redirection vers une autre page après s'être connecté avec une erreur.
    public function displayKanbanAuthenticationFailure() {
        $this->router->POSTredirect('?action=connexion', "Nom d'utilisateur ou mot de passe erroné.");
    }

    // Redirection vers une autre page après s'être déconnecté avec une erreur.
    public function displayKanbanDisconnectionFailure() {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Vous êtes déconnecté.");
    }

    // Redirection vers une autre page après s'être inscrit.
    public function displayKanbanRegistrationSuccess($name) {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Inscription réussie. Vous êtes connecté $name");
    }

    // Redirection vers une autre page après s'être inscrit avec une erreur.
    public function displayKanbanRegistrationFailure() {
        $this->router->POSTredirect($this->router->getRegistrationURL(), "Un champ est invalide.");
    }

    public function displayAddTaskFailure($id) {
        $this->router->POSTredirect("?id=$id", "La tâche n'a pas pu être créée.");
    }

    public function displayAddMemberConfirmationSuccess($id) {
        $this->router->POSTredirect("?id=$id", "Le membre a été ajouté.");
    }

    public function displayAddMemberConfirmationFailure($id) {
        $this->router->POSTredirect("?id=$id", "Le membre n'a pas pu être ajouté.");
    }

    public function displayDeleteMemberConfirmationSuccess($id) {
        $this->router->POSTredirect("?id=$id", "Le membre a bien été supprimé du Kanban.");
    }
}
