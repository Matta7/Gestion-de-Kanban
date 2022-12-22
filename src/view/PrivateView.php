<?php

/*
Classe servant à utilisé un autre type de vue pour les utilisateurs qui sont connectés.
Cela va modifier la page d'accueil et le menu mais le reste est gardé.
*/

class PrivateView extends View {
    private $account;

    public function __construct($title, $content, $router, $feedback, $account) {
        parent::__construct($title, $content, $router, $feedback);
        $this->account = $account;
    }

    public function makeHomePage() {
        $this->title = 'Page d\'accueil';
        $this->content = '<h1> Bienvenue ' . $this->account->getName() . ' ! </h1>\n';
    }

    public function getMenu() {
        $menu = "<nav>\n<ul>\n<li> <a href='index.php'>Page d'accueil</a></li>";
        $menu .= "<li><a href='?liste'>Liste des Kanbans</a></li>\n";
        $menu .= "<li><a id='aPropos' href='?action=" . 'aPropos' . "'>À Propos</a></li>\n";
        $menu .= "<li><a href='?action=nouveau'>Ajouter un kanban</a></li>\n</ul>\n</nav>\n";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getDisconnectionURL() . "';\">Déconnexion</button>\n";

        return $menu;
    }


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
        $this->content .= "</p>\n";
        if($kanban->getCreator() === $this->account->getLogin()){
            $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanAddMemberURL($id) . "';\">Ajouter un membre</button>\n";
            $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanDeleteMemberURL($id) . "';\">Supprimer un membre</button>\n";
        }

        $this->content .= "<div class=\"kanban\" id=\"kanban-" . $id . "\">\n";
        foreach($kanban->getColumns() as $c) {
            $this->content .= "<div id=\"colonne-" . $c->getId() . "\" class=\"colonne\">\n";
            $this->content .= "<h2 class=\"nom-colonne\">" . $c->getName() . "</h2>\n";
            if($kanban->getCreator() === $this->account->getLogin() ||in_array($this->account->getLogin(), $kanban->getMembers())){
                 $this->content .= "<input class\"ajoutTache\" type='button' value='Ajouter une tâche' onclick='addTask(" . $c->getId() . ");'>";
            }
            foreach($c->getTasks() as $t) {
                $this->content .= "<div id=\"tache-" . $t->getId() . "\" class=\"tache\" draggable=\"true\"><p>" . $t->getDesc() . '</p>';
                if($t->getAffectation() != NULL) {
                    $this->content .= '<p>Tâche affectée à : ' . $t->getAffectation() . '</p>';
                }
                $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getAffectationURL($id, $t->getId()) . "'\">Affecter</button>\n 
                </div>\n";
            }
            $this->content .= "</div>\n";
        }
        $this->content .= "</div>\n";
        if($kanban->getCreator() === $this->account->getLogin()){
            $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanUpdateURL($id) . "';\">Modifier</button>\n";
            $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getKanbanAskDeletionURL($id) . "';\">Supprimer</button>\n";
        }
        if($kanban->getImage() != null) {
            $this->content .= "<p><img src='upload/" . $kanban->getImage() . "' alt='" . $kanban->getName() . "'></p>\n";
        }
    }
}
