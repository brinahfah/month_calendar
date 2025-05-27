<?php
// Charger les informations de configuration depuis config.php
$config = require_once 'config.php';

$pdo = null; // Initialise la variable à null

try {
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // <<< CECI EST CRUCIAL !
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Récupère les résultats sous forme de tableau associatif par défaut
        PDO::ATTR_EMULATE_PREPARES => false                 // Désactive l'émulation des requêtes préparées pour plus de sécurité et performance
    ]);

} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Désolé, une erreur technique est survenue lors de la connexion à la base de données. " . $e->getMessage()); // Affiche l'erreur de connexion ici pour le débogage
}
?>