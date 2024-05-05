<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Query the db for number of facts
        $query = "SELECT * FROM facts";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $response['faits'] = $rows;
        $response['message'] = 'Fetched stats successfully.';

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }

    // Return the JSON response
    echo json_encode($response);
}
?>