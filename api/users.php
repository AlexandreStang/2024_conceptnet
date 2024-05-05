<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

include_once '../server/config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Définir et préparer la requête MySQL : Sélectionner tous les utilisateurs
        $query = "SELECT username, score FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['users'] = $rows;
        $response['message'] = 'Récupération réussie de la liste des différentes relations.';
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Méthode de requête invalide.';
}

// Renvoie la réponse JSON
 echo json_encode($response);
?>