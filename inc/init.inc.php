<?php

// CONNEXION BDD
$host = 'mysql:host=localhost;dbname=eboutique';
$login = 'root';
$password = 'root';
$options = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // gestion des erreurs
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' // utf-8
			);

$pdo = new PDO($host, $login, $password, $options);

// Déclaration d'une variable permettant d'afficher des messages utilisateur
$msg = '';

// OUVERTURE D'UNE SESSION
session_start();

// Déclaration de constante
// url absolue du projet
define('URL','http://localhost:8888/php2/eboutique/');
// chemin serveur
define('SERVEUR_ROOT', $_SERVER['DOCUMENT_ROOT']);
// chemin projet
define('SITE_ROOT', '/php2/eboutique/');


// variable pour la nav (mettre les liens en actif en fonction qu'on soit sur la page en question
	
$url = 'http' . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];