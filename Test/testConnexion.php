<?php
require_once '../env/db.php';
require_once '../PDOSquadro.php';

try {
    // Initialisation de la connexion
    PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
