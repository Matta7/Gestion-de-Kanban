<?php

/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once("model/KanbanStorageMySQL.php");
require_once("model/AccountStorageMySQL.php");

/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */


/*
Initialisation de la base de donnée :

$dns = $MYSQL_DNS;
$user = $MYSQL_USER;
$pass = $MYSQL_PASSWORD;
*/

$dns = 'mysql:host=localhost;port=3306;dbname=projet;charset=utf8mb4';
$user = 'projet';
$pass = 'tejorp';

$db = new PDO($dns, $user, $pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Initialisation du routeur et lancement du main (avec les tables créée en amont)
$router = new Router();
$router->main(new KanbanStorageMySQL($db), new AccountStorageMySQL($db));