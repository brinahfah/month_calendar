<?php
        session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil du Calendrier</title>
    <link rel="stylesheet" href="tableaux.css">
    
</head>
<body>
    <div class="main">

        <?php

        // Inclure le fichier de connexion à Supabase
        // Assurez-vous que 'db_connexion.php' pointe bien vers votre connexion Supabase
        // ou renommez-le 'db_supabase_connexion.php' et mettez à jour le require_once
        require_once 'db_connexion.php';
        
        // Inclure la fonction de déconnexion
        // Assurez-vous que le nom du fichier est correct (logout_function.php ou logout_.php)
        require_once 'logout.php'; // Ou 'logout_.php' si c'est votre nom de fichier

        /** @var PDO $pdo */ // Aide pour l'autocomplétion dans certains IDE

        // --- Message de bienvenue ---
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['nom_prenom'])) {
            $prenom = htmlspecialchars($_SESSION['nom_prenom']); // Sécurise l'affichage du nom
            echo "<h1>Bonjour, " . $prenom . " !</h1>";
            // Vous pouvez ajouter un message de bienvenue personnalisé ici
            echo "<p>Bienvenue sur votre tableau de bord. Choisissez une action ci-dessous :</p>";
        } else {
            // Si l'utilisateur n'est pas connecté
            echo "<h1>Bienvenue sur notre site !</h1>";
            echo "<p>Veuillez vous <a href='index.php'>connecter</a> pour accéder à votre espace.</p>";
        }

        // Gérer la déconnexion si le paramètre 'logout' est présent dans l'URL
        // Il est recommandé de rediriger vers un fichier logout.php dédié pour la déconnexion
        // plutôt que de gérer l'action sur la page d'accueil elle-même.
        if (isset($_GET['logout'])) {
            logout(); // Appelle la fonction de déconnexion définie dans logout_function.php
            exit(); // S'assurer que le script s'arrête après la redirection
        }
        
        // Vérifie si l'utilisateur est connecté ET s'il a le rôle "admin"
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
        ?>
            <a href="ajouter.php"><button>Ajouter un programme</button></a>
            <a href="supprimer.php"><button>Supprimer un programme</button></a>
            <a href="create.php"><button>Créer un emploi du temps</button></a>
            <a href="read.php"><button>Voir le calendrier</button></a>
            <a href="index.php?logout=true" class="logout-btn">Déconnexion</a>
        <?php
        } else {
            // Boutons pour les utilisateurs non-admin 
        ?>
            <a href="calendar.php"><button>Voir le calendrier</button></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                 <a href="index.php?logout=true" class="logout-btn">Déconnexion</a>
            <?php endif; ?>
        <?php } ?>
    </div>
</body>
</html>