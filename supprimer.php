<?php
session_start();
require_once 'db_connexion.php'; // Pour la connexion $pdo
require_once 'requete_ajout.php';         // Pour la fonction supprimerProgrammeSemaine

// Annotation PHPDoc pour aider l'éditeur à reconnaître $pdo
/** @var PDO $pdo */

// Sécurité : Rediriger si l'utilisateur n'est pas admin ou non connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: unauthorized.php"); // Assure-toi d'avoir une page unauthorized.php
    exit();
}

$message_status = ''; // Pour stocker les messages de succès/erreur

// --- Traitement de la suppression si un ID est reçu via POST ---
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id_programme_a_supprimer = filter_var($_POST['id_programme'], FILTER_VALIDATE_INT);

    if ($id_programme_a_supprimer !== false && $id_programme_a_supprimer > 0) {
        // APPEL DE LA FONCTION DE SUPPRESSION
        if (supprimerProgrammeSemaine($pdo, $id_programme_a_supprimer)) {
            $message_status = "<p class='message success'>Le programme (ID: {$id_programme_a_supprimer}) a été supprimé avec succès !</p>";
        } else {
            $message_status = "<p class='message error'>Erreur lors de la suppression du programme (ID: {$id_programme_a_supprimer}).</p>";
        }
    } else {
        $message_status = "<p class='message error'>ID de programme invalide pour la suppression.</p>";
    }
}

// --- Récupérer tous les programmes pour l'affichage (avec l'id_school et le nom_prenom) ---
$all_schedules = [];
try {
    $stmt_all_schedules = $pdo->query(
        "SELECT ws.id, ws.jours, ws.cours, ws.heure, s.nom_prenom
         FROM week_schedule ws
         LEFT JOIN schools s ON ws.id_school = s.id_school
         ORDER BY ws.jours, ws.heure"
    );
    $all_schedules = $stmt_all_schedules->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération de tous les programmes : " . $e->getMessage());
    $message_status .= "<p class='message error'>Impossible de charger la liste des programmes.</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Programme</title>
    <link rel="stylesheet" href="tableaux.css">
    <style>
        /* Styles pour les messages de statut */
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        /* Styles pour le tableau de suppression */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-form {
            display: inline-block; /* Pour que le bouton soit sur la même ligne que la cellule */
            margin: 0;
            padding: 0;
        }
        .delete-button {
            background-color: #dc3545; /* Rouge pour la suppression */
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .action-button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }
        .action-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="main">
        <h1>Supprimer un Programme du Calendrier</h1>

        <?= $message_status ?>

        <?php if (!empty($all_schedules)) { ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jour</th>
                        <th>Cours</th>
                        <th>Heure</th>
                        <th>Assigné à</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_schedules as $programme) { ?>
                        <tr>
                            <td><?= htmlspecialchars($programme['id']) ?></td>
                            <td><?= htmlspecialchars($programme['jours']) ?></td>
                            <td><?= htmlspecialchars($programme['cours']) ?></td>
                            <td><?= htmlspecialchars($programme['heure']) ?></td>
                            <td><?= htmlspecialchars($programme['nom_prenom'] ?? 'Non assigné') ?></td>
                            <td>
                                <form action="supprimer.php" method="post" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce programme ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_programme" value="<?= htmlspecialchars($programme['id']) ?>">
                                    <button type="submit" class="delete-button">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>Aucun programme n'est actuellement enregistré.</p>
        <?php } ?>

        <p><a href="calendar.php" class="action-button">Retour à l'accueil</a></p>
    </div>
</body>
</html>