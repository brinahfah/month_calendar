<?php
// Votre code PHP de supprimer.php (ex: vérification de l'ID à supprimer, affichage des détails)
// Assurez-vous d'avoir session_start() et require_once 'db_connexion.php'; ici si nécessaire.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmer la Suppression</title>
    <link rel="stylesheet" href="supprimer.css"> </head>
<body>
    <div class="main-delete"> <h1>Confirmer la suppression du programme</h1>

        <?php
        // Exemple de contenu pour supprimer.php
        // Récupérer l'ID à supprimer depuis l'URL
        $id_to_delete = $_GET['id_personne'] ?? null;
        $program_details = null;

        if ($id_to_delete && is_numeric($id_to_delete)) {
            
           
            require_once 'db_connexion.php'; 

             require_once 'requete.php';

            /** @var PDO $pdo */
            try {
                // Récupérer les détails du programme à supprimer pour confirmation
                $stmt = $pdo->prepare(
                    "SELECT ws.jours, ws.cours, ws.heure,
                            STRING_AGG(s.nom_prenom, ', ') AS assigned_people_names
                     FROM week_schedule ws
                     LEFT JOIN program_assignments pa ON ws.id_week = pa.id_week
                     LEFT JOIN schools s ON pa.id_school = s.id_school
                     WHERE ws.id_week = :id_week
                     GROUP BY ws.id_week, ws.jours, ws.cours, ws.heure"
                );
                $stmt->bindParam(':id_week', $id_to_delete, PDO::PARAM_INT);
                $stmt->execute();
                $program_details = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($program_details) {
                    echo "<p>Voulez-vous vraiment supprimer le programme suivant ? Cette action est irréversible.</p>";
                    echo "<table>";
                    echo "<thead><tr><th>Jour</th><th>Cours</th><th>Heure</th><th>Assigné(s) à</th></tr></thead>";
                    echo "<tbody>";
                    echo "<tr>";
                    echo "<td data-label='Jour'>" . htmlspecialchars($program_details['jours']) . "</td>";
                    echo "<td data-label='Cours'>" . htmlspecialchars($program_details['cours']) . "</td>";
                    echo "<td data-label='Heure'>" . htmlspecialchars($program_details['heure']) . "</td>";
                    echo "<td data-label='Assigné(s) à'>" . htmlspecialchars($program_details['assigned_people_names'] ?: 'Non assigné') . "</td>";
                    echo "</tr>";
                    echo "</tbody>";
                    echo "</table>";

                    // Formulaire de confirmation de suppression
                    echo "<form action='traitement_suppression.php' method='post'>";
                    echo "<input type='hidden' name='id_to_delete' value='" . htmlspecialchars($id_to_delete) . "'>";
                    echo "<button type='submit' class='delete-confirm-button'>Confirmer la suppression</button>";
                    echo "</form>";

                } else {
                    echo "<p style='color: orange;'>Programme non trouvé ou déjà supprimé.</p>";
                }
            } catch (PDOException $e) {
                error_log("Erreur de BDD lors de l'affichage pour suppression : " . $e->getMessage());
                echo "<p style='color: red;'>Erreur lors de la récupération des détails du programme.</p>";
            }
        } else {
            echo "<p style='color: red;'>ID de programme non valide ou manquant.</p>";
        }
        ?>

        <p><a href="calendar.php" class="back-home-button">Retour à l'accueil</a></p>
    </div>
</body>
</html>