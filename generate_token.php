<?php

function generateSecureToken($length = 32) {
    try {
        // Génère des octets aléatoires cryptographiquement sûrs et les convertit en hexadécimal
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        die("Erreur lors de la génération du jeton : " . $e->getMessage());
    }
}

// Appel de la fonction pour générer un jeton
$token = generateSecureToken(); // Longueur par défaut de 32 caractères
echo "Votre jeton sécurisé : " . $token;
