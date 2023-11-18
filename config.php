<?php

require_once 'vendor/autoload.php';

// Charge les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Construction du tableau de configuration de l'utilisateur à partir des variables d'environnement
$userConfig = [
    'MYGES_USERNAME' => $_ENV['MYGES_USERNAME'] ?? 'valeur_par_defaut_username',
    'MYGES_PASSWORD' => $_ENV['MYGES_PASSWORD'] ?? 'valeur_par_defaut_password',
    'TOKEN' => $_ENV['TOKEN'] ?? 'valeur_par_defaut_token'
];

define('USER_CONFIG', $userConfig);

