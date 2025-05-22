<?php
// --- LIGNES DE DÉBOGAGE AGRESSIVES : À RETIRER ABSOLUMENT EN PRODUCTION ! ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN LIGNES DE DÉBOGAGE ---

header('Content-Type: application/json'); // Indique que la réponse sera du JSON

session_start();
require_once 'db_connexion.php'; // Pour la connexion $pdo
require_once 'requete_ajout.php'; // Pour la fonction supprimerProgrammeSemaine

// Annotation PHPDoc pour aider l'éditeur à reconnaître $pdo
/** @var PDO $pdo */

$response = ['success' => false, 'message' => ''];

// --- Débogage : Afficher le contenu de la session ---
error_log("api_delete_program.php: Session dump: " . print_r($_SESSION, true));

// Sécurité : Vérifier si l'utilisateur est admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    $response['message'] = "Accès non autorisé.";
    http_response_code(403); // Code HTTP 403 Forbidden
    echo json_encode($response);
    exit();
}

// --- Débogage : Afficher les données POST reçues ---
error_log("api_delete_program.php: POST dump: " . print_r($_POST, true));

// Vérifier si la requête est bien un POST et si l'ID est présent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_programme'])) {
    $id_programme_a_supprimer = filter_var($_POST['id_programme'], FILTER_VALIDATE_INT);

    if ($id_programme_a_supprimer !== false && $id_programme_a_supprimer > 0) {
        try {
            if (supprimerProgrammeSemaine($pdo, $id_programme_a_supprimer)) {
                $response['success'] = true;
                $response['message'] = "Programme (ID Semaine: {$id_programme_a_supprimer}) supprimé avec succès.";
            } else {
                $response['message'] = "Échec de la suppression du programme (ID Semaine: {$id_programme_a_supprimer}).";
            }
        } catch (PDOException $e) {
            $response['message'] = "Erreur de base de données : " . $e->getMessage();
            error_log("PDOException in api_delete_program.php: " . $e->getMessage());
            http_response_code(500); // Code HTTP 500 Internal Server Error
        }
    } else {
        $response['message'] = "ID de programme invalide.";
        http_response_code(400); // Code HTTP 400 Bad Request
    }
} else {
    $response['message'] = "Requête invalide (pas POST ou ID manquant)."; // Message plus précis
    http_response_code(400); // Code HTTP 400 Bad Request
}

echo json_encode($response);
exit();
?>