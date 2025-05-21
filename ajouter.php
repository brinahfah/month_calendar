<?php
session_start();
require_once 'db_connexion.php'; // Pour la connexion $pdo
require_once 'requete_ajout.php';         // Pour la fonction ajouterProgrammeSemaine

// Annotation PHPDoc pour aider l'éditeur à reconnaître $pdo
/** @var PDO $pdo */

// Sécurité : Rediriger si l'utilisateur n'est pas admin ou non connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: unauthorized.php"); // Assure-toi d'avoir une page unauthorized.php
    exit();
}

// Récupérer la liste des personnes (écoles) pour le champ de sélection
$personnes_list = [];
try {
    // Sélectionne id_school et nom_prenom de la table schools
    $stmt_personnes = $pdo->query("SELECT id_school, nom_prenom FROM schools ORDER BY nom_prenom");
    $personnes_list = $stmt_personnes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des personnes : " . $e->getMessage());
    echo "<p class='message error'>Impossible de charger la liste des personnes pour l'assignation.</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Programme Spécifique</title>
    <link rel="stylesheet" href="tableaux.css">
    <style>
        /* Styles pour le formulaire (tu peux les déplacer dans tableaux.css si tu préfères) */
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 20px auto;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="time"],
        form input[type="number"],
        form select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Inclure le padding et le border dans la largeur */
        }
        form button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #218838;
        }
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
    </style>
</head>
<body>
    <div class="main">
        <h1>Ajouter un Programme Spécifique au Calendrier</h1>

        <?php
        // Traitement du formulaire lorsque des données sont envoyées en POST
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Nettoyage et validation des entrées utilisateur
            $jour_saisi = trim($_POST['jours']);
            $cours_saisi = trim($_POST['cours']);
            $heure_saisi = trim($_POST['heure']);
            $id_week_saisi = filter_var(trim($_POST['id_week']), FILTER_VALIDATE_INT);
            $id_school_selectionne = filter_var(trim($_POST['id_school_assign']), FILTER_VALIDATE_INT); // Récupération de l'ID de la personne sélectionnée

            // Validation de base : s'assurer que tous les champs sont remplis et valides
            if (empty($jour_saisi) || empty($cours_saisi) || empty($heure_saisi) ||
                $id_week_saisi === false || $id_week_saisi <= 0 ||
                $id_school_selectionne === false || $id_school_selectionne <= 0) {
                echo "<p class='message error'>Veuillez remplir tous les champs correctement et sélectionner une personne valide.</p>";
            } else {
                // APPEL DE LA FONCTION AVEC TOUS LES PARAMÈTRES, Y COMPRIS L'ID DE LA PERSONNE
                if (ajouterProgrammeSemaine($pdo, $jour_saisi, $cours_saisi, $heure_saisi, $id_week_saisi, $id_school_selectionne)) {
                    // Optionnel : retrouver le nom de la personne pour l'afficher dans le message de succès
                    $nom_personne_ajoute = "la personne sélectionnée";
                    foreach ($personnes_list as $p) {
                        if ($p['id_school'] == $id_school_selectionne) {
                            $nom_personne_ajoute = htmlspecialchars($p['nom_prenom']);
                            break;
                        }
                    }
                    echo "<p class='message success'>Le programme a été ajouté avec succès pour **" . $nom_personne_ajoute . "** !</p>";
                } else {
                    echo "<p class='message error'>Erreur lors de l'ajout du programme. Veuillez réessayer.</p>";
                }
            }
        }
        ?>

        <form action="ajouter.php" method="post">

         <label for="id_school_assign">Assigner ce programme à :</label>
            <select id="id_school_assign" name="id_school_assign" required>
                <option value="">-- Sélectionnez une personne --</option>
                <?php
                if (!empty($personnes_list)) {
                    foreach ($personnes_list as $personne) {
                        echo '<option value="' . htmlspecialchars($personne['id_school']) . '">' . htmlspecialchars($personne['nom_prenom']) . '</option>';
                    }
                } else {
                    echo '<option value="">Aucune personne trouvée</option>';
                }
                ?>
            </select>

            <label for="jours">Jour :</label>
            <select id="jours" name="jours" required>
                <option value="">Sélectionnez un jour</option>
                <option value="Lundi">Lundi</option>
                <option value="Mardi">Mardi</option>
                <option value="Mercredi">Mercredi</option>
                <option value="Jeudi">Jeudi</option>
                <option value="Vendredi">Vendredi</option>
                <option value="Samedi">Samedi</option>
                <option value="Dimanche">Dimanche</option>
            </select>

            <label for="cours">Cours/Événement :</label>
            <input type="text" id="cours" name="cours" required>

            <label for="heure">Heure :</label>
            <input type="time" id="heure" name="heure" required>

            

           
            <button type="submit">Ajouter au Calendrier</button>
        </form>

        <a href="calendar.php"><button>Retour à l'accueil</button></a>
    </div>
</body>
</html>