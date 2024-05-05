<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

ini_set('allow_url_fopen', true);

// Récupérer les attributs pour la requête
$langue = $_GET['langue'];
$concept = $_GET['concept'];

// Assembler la requête API
$queryURL = "https://api.conceptnet.io/c/$langue/$concept";

// Assembler la requête API
$response = file_get_contents($queryURL);

// Vérifier si la requête a été un succès
if ($response) {
    echo stripslashes($response);
} else {
    echo json_encode(['error' => "Échec de l'exécution de la requête API"]);
}

?>