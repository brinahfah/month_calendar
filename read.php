<?php
session_start(); // Assurez-vous que la session est démarrée
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'bdd/db_connexion.php'; // Votre fichier de connexion à la base de données
// require_once 'requete.php'; // Incluez si vous avez des fonctions de récupération ici

/** @var PDO $pdo */

$is_admin = (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['role']) && $_SESSION['role'] == "admin");
$current_user_id = $_SESSION['id_school'] ?? null; // Assurez-vous que l'id_school est stocké en session lors de la connexion

$programmes = [];
$sql = "SELECT ws.id_week, ws.jours, ws.cours, ws.heure, s.nom_prenom as assigned_to
        FROM week_schedule ws
        LEFT JOIN program_assignments pa ON ws.id_week = pa.id_week
        LEFT JOIN schools s ON pa.id_school = s.id_school";
$params = [];

if ($is_admin) {
    // Si l'utilisateur est admin, il ne voit que ses propres programmes
    $sql .= " WHERE pa.id_school = :user_id";
    $params[':user_id'] = $current_user_id;
}
// else {
//    // Pour les utilisateurs non-admin (prof, étudiant), vous pouvez choisir:
//    // - Voir TOUS les programmes (comme avant, pas de clause WHERE)
//    // - Voir seulement leurs propres programmes (même WHERE que l'admin)
//    // - Voir les programmes des professeurs (selon leur rôle dans 'schools' si vous avez cette colonne)
//    // Pour cet exemple, je suppose qu'ils voient TOUS les programmes non-filtrés s'ils ne sont pas admins
// }


$sql .= " ORDER BY ws.jours, ws.heure"; // Ajout d'un ORDER BY pour un affichage cohérent

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $programmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur de récupération des programmes dans read.php: " . $e->getMessage());
    $error_message = "Erreur lors du chargement des programmes.";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Programmes</title>
    <link rel="stylesheet" href="css/tableaux.css"> 
</head>
<body>
    <div class="main">
        <h1><?php echo $is_admin ? "Mon Calendrier" : "Calendrier des Programmes"; ?></h1>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if (!empty($programmes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Cours</th>
                        <th>Heure</th>
                        <th>Assigné à</th>
                        <?php if ($is_admin): ?>
                            <th>Actions</th> <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programmes as $programme): ?>
                    <tr>
                        <td data-label="Jour"><?= htmlspecialchars($programme['jours']) ?></td>
                        <td data-label="Cours"><?= htmlspecialchars($programme['cours']) ?></td>
                        <td data-label="Heure"><?= htmlspecialchars($programme['heure']) ?></td>
                        <td data-label="Assigné à"><?= htmlspecialchars($programme['assigned_to'] ?: 'Non assigné') ?></td>
                        <?php if ($is_admin): ?>
                            <td data-label="Actions">
                                <a href="modifier.php?id=<?= htmlspecialchars($programme['id_week']) ?>" class="action-button edit-button">Modifier</a>
                                <a href="supprimer.php?id=<?= htmlspecialchars($programme['id_week']) ?>" class="action-button delete-button">Supprimer</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun programme trouvé pour l'instant.</p>
        <?php endif; ?>

        <div class="button-container" style="margin-top: 30px;">
            <a href="calendar.php" class="action-button">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>