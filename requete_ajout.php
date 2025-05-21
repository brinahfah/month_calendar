<?php

/**
 * Ajoute un nouveau programme (cours ou événement) à la table week_schedule.
 *
 * @param PDO    $pdo        L'objet PDO de connexion à la base de données.
 * @param string $jour       Le jour du programme (ex: 'Lundi', 'Mardi').
 * @param string $cours      Le nom du cours/événement.
 * @param string $heure      L'heure du programme (format 'HH:MM').
 * @param int    $id_week    L'ID de la semaine (clé étrangère).
 * @param int    $id_school  L'ID de l'école/personne à qui ce programme est rattaché. // Nouveau paramètre
 * @return bool True si l'insertion réussit, False sinon.
 */
function ajouterProgrammeSemaine(PDO $pdo, string $jour, string $cours, string $heure, int $id_week, int $id_school): bool
{
    try {
        // La requête SQL doit inclure la nouvelle colonne id_school
        $stmt = $pdo->prepare(
            "INSERT INTO week_schedule (id_week, jours, cours, heure, id_school)
             VALUES (:id_week, :jours, :cours, :heure, :id_school)"
        );

        // Liaison des paramètres, y compris le nouvel id_school
        $stmt->bindParam(':id_week', $id_week, PDO::PARAM_INT);
        $stmt->bindParam(':jours', $jour, PDO::PARAM_STR);
        $stmt->bindParam(':cours', $cours, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $heure, PDO::PARAM_STR);
        $stmt->bindParam(':id_school', $id_school, PDO::PARAM_INT); // Liaison du nouveau paramètre

        $result = $stmt->execute();
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur d'insertion dans week_schedule: " . $e->getMessage());
        return false;
    }
}

// ... d'autres fonctions utilitaires si tu en as ...

/**
 * Supprime un programme de la table week_schedule par son ID unique.
 *
 * @param PDO $pdo L'objet PDO de connexion à la base de données.
 * @param int $id_programme L'ID unique du programme à supprimer.
 * @return bool True si la suppression réussit, False sinon.
 */
function supprimerProgrammeSemaine(PDO $pdo, int $id_programme) : bool
{
    try {
        $stmt = $pdo->prepare("DELETE FROM week_schedule WHERE id = :id");
        $stmt->bindParam(':id', $id_programme, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur de suppression dans week_schedule: " . $e->getMessage());
        return false;
    }
}


// ... (d'autres fonctions si vous en avez) ...

?>