<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = [
        "error" => false,
        "message" => "This is the help page. You can use the following endpoints:",
        "endpoints" => [
            "GET /concepts" => "Fetch a list of all concepts.",
            "GET /relations" => "Fetch a list of all relations.",
            "GET /users" => "Fetch a list of all users.",
            "POST /user-create" => "Create a new user. Requires username and password in POST data.",
            "GET /help" => "Displays help information."
        ]
    ];
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request method.';
}

// Return the JSON response
echo json_encode($response);
?>