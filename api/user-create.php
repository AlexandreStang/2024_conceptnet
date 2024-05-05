<?php
include_once '../server/config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are provided
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        try {
            // Query the db
            $query = "INSERT INTO users (username, password) VALUES (:username, :password);";

            $stmt = $db->prepare($query);

            // Bind parameters and run the query
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // return to js client that login was successful
            if ($stmt->rowCount() == 1) {
                // Successful Insert
                $response['username'] = $username; // Use the input username directly
                $response['message'] = 'User created successfully.';
            } else {
                // No rows affected, no insert done
                $response['error'] = true;
                $response['message'] = 'No user was created.';
            }

        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
            // return to js client that login failed
            $response['error'] = true;
            $response['message'] = "Error: " . $e->getMessage();
        }
        // Return the JSON response
        echo json_encode($response);
    } else {
        $response['error'] = true;
        $response['message'] = 'Missing username or password.';
        echo json_encode($response);
        exit;
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request method.';
}
?>