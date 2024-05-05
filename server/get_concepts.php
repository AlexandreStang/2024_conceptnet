<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once 'config/database.php';

// Initialiser la connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

// Définir et préparer la requête MySQL
$query = "SELECT * FROM facts";
$stmt = $conn->prepare($query);

// Exécuter la requête MySQL
if ($stmt->execute()) {
    // Encode le résultat en json
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} else {
    // Erreur
    http_response_code(500); // Set HTTP response code to 500 (Internal Server Error)
    echo json_encode(['error' => 'Failed to execute query']);
}