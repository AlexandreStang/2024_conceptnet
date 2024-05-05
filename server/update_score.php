<?php
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $score = intval($_POST['score']);
    $current_score = null;
    $new_score = null;

    try {
        // Query the db
        $query = "SELECT score FROM users WHERE username = :username";
        $stmt = $db->prepare($query);

        // Bind parameters and run the query
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            if ($row = $stmt->fetch()) {
                // Update and prepare the new score;
                $current_score = $row['score'];
                $new_score = $current_score + $score;
            }
        } else {
            $error = "Invalid username.";
            // return to js client that login failed
            $response['error'] = true;
            $response['message'] = 'Invalid username.';
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        // return to js client that login failed
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }

    try {
        // Query the db
        $query = "UPDATE users SET score = :score WHERE username = :username";
        $stmt = $db->prepare($query);

        // Bind parameters and run the query
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':score', $new_score);

        if ($stmt->execute()) {
            // Score updated successfully
            $response["new_score"] = $new_score;
        } else {
            $error = "Invalid username or score.";
            // return to js client that login failed
            $response['error'] = true;
            $response['message'] = 'Invalid username or score.';
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        // return to js client that login failed
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }
    // Return the JSON response

    echo json_encode($response);
}