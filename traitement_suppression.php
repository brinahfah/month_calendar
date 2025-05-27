<?php
session_start(); // Démarrez la session si vous en avez besoin
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connexion.php'; // Incluez votre fichier de connexion à la base de données
require_once 'requete.php';     // Incluez votre fichier de fonctions de requête (là où se trouve supprimerProgrammeSemaine)

/** @var PDO $pdo */ // Pour l'autocomplétion dans certains IDE

// Vérifiez si la requête provient d'une soumission POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_to_delete = $_POST['id_to_delete'] ?? null;

    if ($id_to_delete && is_numeric($id_to_delete)) {
        $id_to_delete = (int)$id_to_delete; // Assurez-vous que c'est un entier

        try {
            // Appelez la fonction de suppression
            $success = supprimerProgrammeSemaine($pdo, $id_to_delete);

            if ($success) {
                // Redirection en cas de succès vers la page d'accueil (calendar.php)
                header('Location: calendar.php?status=success&message=Programme_supprime_avec_succes');
                exit();
            } else {
                // Cette partie est moins probable si la fonction lance une exception
                // mais c'est une sécurité.
                header('Location: calendar.php?status=error&message=Echec_suppression_programme');
                exit();
            }
        } catch (PDOException $e) {
            // En cas d'erreur de base de données interceptée par la fonction
            error_log("Erreur fatale lors de la suppression du programme ID " . $id_to_delete . ": " . $e->getMessage());
            header('Location: calendar.php?status=error&message=Erreur_base_de_donnees_suppression');
            exit();
        } catch (Exception $e) {
            // Pour toute autre erreur inattendue
            error_log("Erreur inattendue lors de la suppression du programme ID " . $id_to_delete . ": " . $e->getMessage());
            header('Location: calendar.php?status=error&message=Erreur_generale_suppression');
            exit();
        }
    } else {
        // Redirection si l'ID est manquant ou invalide
        header('Location: calendar.php?status=error&message=ID_programme_invalide');
        exit();
    }
} else {
    // Si la page est accédée directement sans soumission POST
    header('Location: calendar.php?status=error&message=Acces_non_autorise');
    exit();
}
?>