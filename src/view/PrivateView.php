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
        $menu = "<nav>\n<ul>\n<li> <a href='fromages.php'>Page d'accueil</a></li>";
        $menu .= "<li><a href='?liste'>Liste des fromages</a></li>\n";
        $menu .= "<li><a id='aPropos' href='?action=" . 'aPropos' . "'>À Propos</a></li>\n";
        $menu .= "<li><a href='?action=nouveau'>Ajouter un fromage</a></li>\n</ul>\n</nav>\n";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getDisconnectionURL() . "';\">Déconnexion</button>\n";

        return $menu;
    }
}
