<?php

class View {

    private $title;
    private $content;
    protected $router;
    private $feedback;

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
        $this->content .= "<div class=\"kanban\">\n";
        foreach($kanban->getColumns() as $c) {
            $this->content .= "<div id=\"colonne-" . $c->getId() . "\" class=\"colonne\">\n";
            $this->content .= "<h2 class=\"nom-colonne\">" . $c->getName() . "</h2>\n";
            foreach($c->getTasks() as $t) {
                $this->content .= "<div id=\"tache-" . $t->getId() . "\" class=\"tache\" draggable=\"true\">" . $t->getDesc() . "</div>\n";
            }
            $this->content .= "</div>\n";
        }
        $this->content .= "</div>\n";

        $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanUpdateURL($id) . "';\">Modifier</button>\n";
        $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanAskDeletionURL($id) . "';\">Supprimer</button>\n";
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
        $this->content = "<h1>Voulez vous vraiment supprimer ce kanban ?</h1>\n<form action='".$this->router->getKanbanDeletionURL($id)."' method='POST'>\n
        <button>Supprimer</button>\n
        </form>\n";
    }

    // Page de modification d'objet.
    public function makeKanbanUpdatePage($id, $kanbanBuilder = null) {
        $kanban = $kanbanBuilder->createKanban();
        if($kanbanBuilder->getError() != null) {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' value='" . $kanban->getName() . "' />" . $kanbanBuilder->getError()['name'] . "</p>\n
            <p>Region du fromage : <input type='text' name='region' value='" . $kanban->getRegion() . "' />" . $kanbanBuilder->getError()['region'] . "</p>\n
            <p>Année de creation du fromage : <input type='text' name='year' value='" . $kanban->getYear() . "' />" . $kanbanBuilder->getError()['year'] . "</p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'>" . $kanbanBuilder->getError()['image'] . "</p>\n
            <p><input type='submit' value='Modifier'></p>\n
            </form>\n";
        }

        else {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getKanbanUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' value='" . $kanban->getName() . "' /></p>\n
            <p>Region du fromage : <input type='text' name='region' value='" . $kanban->getRegion() . "' /></p>\n
            <p>Année de creation du fromage : <input type='text' name='year' value='" . $kanban->getYear() . "' /></p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'></p>\n
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

    // Page "à propos".
    public function makeAProposPage(){
        $this->title = 'À propos';
        $this->content="<p>Numéro de groupe : Groupe 64</p>\n";
        $this->content.="<p>Numéros étudiants des membres du groupe : 21910887 & 21908377</p>\n";
        $this->content.="<p>Nous avons réalisé toutes les fonctionnalité de base, c'est à dire, la création d'objet ainsi que l'authentification d'un compte, tout deux répertoriés dans une base de donnée MySQL.</p>\n";
        $this->content.="<p>Un utilisateur connecté peut ainsi ajouter des objets, modifier et supprimer l'objet qu'il crée, sauf dans le cas où son statue est admin.</p>\n";
        $this->content.="<p>Nous avons aussi implémenter la création de compte, un visiteur lambda peut ainsi se créer un compte.</p>\n";
        $this->content.="<p>Parmi les compléments suggérés, nous avons réaliser :</p>\n";
        $this->content.="<ul>\n<li>(*) Une recherche d'objets, nous pouvons ainsi rechercher un objet à partir d'une chaine de caractère. La recherche d'objet est accessible depuis la liste des fromages.</li>\n<li>(*) Associer des images aux obets, un objet peut être illustré par zéro ou une image modifiable par le créateur de l'objet ou l'admin.</li>\n<li>En troisième et dernier complément, nous avons implémenter le système de pagination. Il s'applique lorsque la liste des objets est affiché, n'est pas utilisé lors de la recherche mais le pourrait.</li></ul>\n";
        $this->content.="<p>Répartition des tâches : nous nous sommes répartis les tâches surtout au sein du model. Nous avons ainsi globalement fait la vue et le controller ensemble.</p>\n";
        $this->content.="<p>WILLENBUCHER Gurvan s'est occupé de la création d'objets et de saisir les URL pour la vue, ainsi que le CSS.</p>\n";
        $this->content.="<p>VALLÉE Mathieu s'est occupé de la partie authentification des comptes et création, et aussi la gestion de la base de donnée.</p>\n";
        $this->content.="<p>Pour ce qui est des principaux choix en matière de design, nous avons suivi l'ordre des TP des séances 12 à 17. Nous avons ainsi une structure MVCR comme demandée dans l'énoncé.</p>\n";
        $this->content.="<p>Le model représente toutes les données du site, intéractions avec la base de donnée comprises, la vue va afficher, sans modifier le model toutes les pages, le controller va nous permettre d'intéragir entre le model et la vue et le routeur nous permet de gérer les URL, créer la vue et le controller ainsi qu'à gérer les droits des utilisateurs.</p>\n";
        $this->content.="<p>Note : nous avions commencé à implémenter le responsive, que nous avons abandonné depuis pour un autre complément. Ainsi nous avions surement laissé des traces.</p>\n";
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
}
