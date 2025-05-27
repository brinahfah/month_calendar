<?php
session_start(); // DOIT ÊTRE LA TOUTE PREMIÈRE CHOSE DANS LE FICHIER PHP

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'logout.php'; // Assurez-vous que ce chemin est correct
require_once 'bdd/db_connexion.php'; // Assurez-vous que ce chemin est correct
 
/** @var PDO $pdo */ // Aide pour l'autocomplétion dans certains IDE

// Gérer la déconnexion si le paramètre 'logout' est présent dans l'URL
if (isset($_GET['logout'])) {
    logout(); // Appelle la fonction de déconnexion définie dans logout.php
    exit(); // S'assurer que le script s'arrête après la redirection
}

// --- Message de bienvenue ---
$prenom = ""; // Initialisation pour éviter le warning Undefined variable
$welcome_message = "";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['nom_prenom'])) {
    $prenom = htmlspecialchars($_SESSION['nom_prenom']); // Sécurise l'affichage du nom
    $welcome_message = "<h1>Bonjour, " . $prenom . " !</h1>";
    $welcome_message .= "<p>Bienvenue sur votre tableau de bord. Choisissez une action ci-dessous :</p>";
} else {
    // Si l'utilisateur n'est pas connecté
    $welcome_message = "<h1>Bienvenue sur notre site !</h1>";
    $welcome_message .= "<p>Veuillez vous <a href='index.php'>connecter</a> pour accéder à votre espace.</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil du Calendrier</title>
    <link rel="stylesheet" href="css/tableaux.css"> 
</head>
<body>
    <div class="main">
        <?= $welcome_message ?>

        <?php
        // Vérifie si l'utilisateur est connecté ET s'il a le rôle "admin"
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
        ?>
            <a href="ajouter.php" class="action-button">Ajouter un programme</a>
            <a href="supprimer.php" class="action-button delete-button">Supprimer un programme</a> 
            <a href="create.php" class="action-button">Créer un emploi du temps</a>
            <a href="read.php" class="action-button">Voir le calendrier</a>
            <a href="index.php?logout=true" class="action-button logout-btn">Déconnexion</a>
        <?php
        } else {
            // Boutons pour les utilisateurs non-admin 
        ?>
            <a href="read.php" class="action-button">Voir le calendrier</a> 
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <a href="index.php?logout=true" class="action-button logout-btn">Déconnexion</a>
            <?php endif; ?>
        <?php } ?>
    </div>
</body>
</html>