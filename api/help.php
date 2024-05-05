<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = [
        "error" => false,
        "message" => "This is the help page. You can use the following endpoints:",
        "endpoints" => [
            ["method" => "GET", "route" => "/concepts", "description" => "Récupérez une liste de tous les concepts."],
            ["method" => "GET", "route" => "/relations", "description" => "Récupérez une liste de toutes les relations."],
            ["method" => "GET", "route" => "/users", "description" => "Récupérez une liste de tous les utilisateurs."],
            ["method" => "POST", "route" => "/user-create", "description" => "Créez un nouvel utilisateur. Nécessite un 
                nom d'utilisateur et un mot de passe dans les données POST."],
            ["method" => "GET", "route" => "/help", "description" => "Affiche des informations d'aide."]
        ]
    ];
} else {
    $response['error'] = true;
    $response['message'] = 'Méthode de requête invalide.';
}

// Renvoie la réponse JSON
echo json_encode($response);
?>