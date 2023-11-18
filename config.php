<?php

require_once 'vendor/autoload.php';

// Charge les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Constant name can be the firstname of the user
// Exemple : define('MATTHIEU', array('MYGES_USERNAME' => 'CHANGEME', 'MYGES_PASSWORD' => 'CHANGEME'));
define('CHANGEME', array(
    'MYGES_USERNAME' => $_ENV['MYGES_USERNAME'], 
    'MYGES_PASSWORD' => $_ENV['MYGES_PASSWORD']
));

