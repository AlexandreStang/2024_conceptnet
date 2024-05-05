<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

ini_set('allow_url_fopen', true);

// Récupérer les attributs pour la requête
$relation = urlencode($_GET['relation']);

// Assembler la requête API
$query = "rel=/r/$relation";
$queryURL = "https://api.conceptnet.io/query?$query&limit=100";

// Assembler la requête API
$response = file_get_contents($queryURL);

// Vérifier si la requête a été un succès
if ($response) {
    echo stripslashes($response);
} else {
    echo json_encode(['error' => "Échec de l'exécution de la requête API"]);
}

?>