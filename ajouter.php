<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un nouveau programme</title>
    <link rel="stylesheet" href="ajouter.css"> </head>
<body>
    <div class="main-add"> <h1>Ajouter un nouveau programme</h1>

        <form action="traitement_ajout.php" method="post"> <div class="form-group">
                <label for="jour">Jour de la semaine :</label>
                <select id="jour" name="jour" required>
                    <option value="">Sélectionnez un jour</option>
                    <option value="Lundi">Lundi</option>
                    <option value="Mardi">Mardi</option>
                    <option value="Mercredi">Mercredi</option>
                    <option value="Jeudi">Jeudi</option>
                    <option value="Vendredi">Vendredi</option>
                    <option value="Samedi">Samedi</option>
                    <option value="Dimanche">Dimanche</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cours">Cours :</label>
                <input type="text" id="cours" name="cours" required>
            </div>

            <div class="form-group">
                <label for="heure">Heure :</label>
                <input type="time" id="heure" name="heure" required>
            </div>

            <div class="form-group">
                <label for="id_personne_assignee">Assigner à (personne) :</label>
                <select id="id_personne_assignee" name="id_personne_assignee" required>
                    <option value="">Sélectionnez une personne</option>
                    <?php
                    // Vous devrez inclure db_connexion.php ici et récupérer la liste des personnes
                    require_once 'db_connexion.php'; // Incluez db_connexion.php ici

                   

                    /** @var PDO $pdo */
                    
                    try {
                        $stmt_personnes = $pdo->query("SELECT id_school, nom_prenom FROM schools ORDER BY nom_prenom");
                        while ($personne = $stmt_personnes->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($personne['id_school']) . '">' . htmlspecialchars($personne['nom_prenom']) . '</option>';
                        }
                    } catch (PDOException $e) {
                        error_log("Erreur lors de la récupération des personnes pour le formulaire d'ajout : " . $e->getMessage());
                        echo '<option value="">Erreur de chargement des personnes</option>';
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="submit-button">Ajouter le programme</button>
           <p><a href="calendar.php" class="action-button">Retour à l'accueil</a></p>
        </form>
    </div>
</body>
</html>