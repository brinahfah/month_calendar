<?php
session_start(); // Assurez-vous que la session est démarrée ici si vous l'utilisez
require_once 'bdd/db_connexion.php'; // Incluez votre fichier de connexion à la base de données

/** @var PDO $pdo */ // Pour l'autocomplétion dans certains IDE

// Vérifiez si le formulaire a été soumis via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $jour = $_POST['jour'] ?? null;
    $cours = $_POST['cours'] ?? null;
    $heure = $_POST['heure'] ?? null;
    $id_personne_assignee = $_POST['id_personne_assignee'] ?? null;

    // Validation simple des données
    if (empty($jour) || empty($cours) || empty($heure) || empty($id_personne_assignee)) {
        // En cas d'erreur de validation, redirigez avec un message si besoin
        // Ou affichez un message d'erreur si vous voulez rester sur cette page de traitement
        error_log("Erreur de validation lors de l'ajout de programme : champs manquants.");
        // Redirection vers ajouter.php avec un paramètre d'erreur, par exemple
        header('Location: ajouter.php?status=error&message=Champs_manquants');
        exit();
    }

    try {
        $pdo->beginTransaction(); // Démarrez une transaction pour garantir l'intégrité des données

        // NOTE TRÈS IMPORTANTE :
        // La logique d'insertion ici dépend de votre structure de base de données.
        // Si `week_schedule` est une liste fixe de programmes et que vous faites juste des ASSIGNATIONS,
        // vous ne devriez PAS insérer dans `week_schedule` ici. Vous devriez passer l'ID du programme
        // à assigner (id_week) depuis `ajouter.php` et seulement insérer dans `program_assignments`.

        // SI VOUS VOULEZ INSERER UN NOUVEAU PROGRAMME ET L'ASSIGNER (ce que fait le code ci-dessous) :
        // 1. Insérer le nouveau programme dans `week_schedule`
        $stmt_insert_ws = $pdo->prepare("INSERT INTO week_schedule (jours, cours, heure) VALUES (:jours, :cours, :heure) RETURNING id_week"); // RETURNING id_week pour PostgreSQL
        $stmt_insert_ws->bindParam(':jours', $jour);
        $stmt_insert_ws->bindParam(':cours', $cours);
        $stmt_insert_ws->bindParam(':heure', $heure);
        $stmt_insert_ws->execute();
        $id_week_nouveau = $stmt_insert_ws->fetchColumn(); // Récupère l'ID du nouveau programme inséré

        if (!$id_week_nouveau) {
            throw new Exception("Échec de l'insertion du programme dans week_schedule.");
        }

        // 2. Insérer l'assignation dans `program_assignments`
        $stmt_insert_pa = $pdo->prepare("INSERT INTO program_assignments (id_school, id_week) VALUES (:id_school, :id_week)");
        $stmt_insert_pa->bindParam(':id_school', $id_personne_assignee, PDO::PARAM_INT);
        $stmt_insert_pa->bindParam(':id_week', $id_week_nouveau, PDO::PARAM_INT);
        $stmt_insert_pa->execute();

        $pdo->commit(); // Confirmez la transaction si tout s'est bien passé

        // Redirection vers calendar.php après un ajout réussi
        header('Location: calendar.php?status=success&message=Programme_ajoute'); // Ajout d'un message de succès
        exit(); // Toujours appeler exit() après une redirection pour s'assurer que le script s'arrête.

    } catch (PDOException $e) {
        $pdo->rollBack(); // Annulez la transaction en cas d'erreur
        error_log("Erreur PDO lors de l'ajout de programme : " . $e->getMessage());
        header('Location: ajouter.php?status=error&message=Erreur_base_de_donnees');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erreur inattendue lors de l'ajout de programme : " . $e->getMessage());
        header('Location: ajouter.php?status=error&message=Erreur_inattendue');
        exit();
    }

} else {
    // Si la page est accédée directement sans soumission POST
    header('Location: ajouter.php?status=error&message=Acces_non_autorise');
    exit();
}
?>