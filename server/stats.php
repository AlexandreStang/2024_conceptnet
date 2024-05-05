<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Définir et préparer la requête MySQL : Calculer le nombre de faits
        $query1 = "SELECT count(*) AS number_of_facts FROM facts";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();
        $number_of_facts = $stmt1->fetch()['number_of_facts'];


        // Définir et préparer la requête MySQL : Calculer le nombre de concepts
        $query2 = "SELECT COUNT(DISTINCT start) AS number_of_concepts FROM facts";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute();
        $number_of_concepts = $stmt2->fetch()['number_of_concepts'];

        // Définir et préparer la requête MySQL : Calculer le nombre de relations
        $query3 = "SELECT COUNT(DISTINCT relation) AS number_of_relations FROM facts";
        $stmt3 = $db->prepare($query3);
        $stmt3->execute();
        $number_of_relations = $stmt3->fetch()['number_of_relations'];

        // Définir et préparer la requête MySQL : Calculer le nombre d'utilisateurs
        $query4 = "SELECT COUNT(*) AS number_of_users FROM users";
        $stmt4 = $db->prepare($query4);
        $stmt4->execute();
        $number_of_users = $stmt4->fetch()['number_of_users'];

        $response['number_of_facts'] = $number_of_facts;
        $response['number_of_concepts'] = $number_of_concepts;
        $response['number_of_relations'] = $number_of_relations;
        $response['number_of_users'] = $number_of_users;
        $response['message'] = 'Statistiques récupérées avec succès.';

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }

    // Renvoie la réponse JSON
    echo json_encode($response);
}
?>