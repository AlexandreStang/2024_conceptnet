<?php
include_once '../server/config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Query for number of relations:
        $query = "SELECT username, score FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['users'] = $rows;
        $response['message'] = 'Fetched the list of the different relations successfully';
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request method.';
}

 // Return the JSON response
 echo json_encode($response);
?>