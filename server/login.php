<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

// Vérifier si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Vérifier s'il existe un utilisateur qui partage le même nom d'utilisateur et mot de passe
        $query = "SELECT username, password FROM users WHERE username = :username AND password = :password";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            if ($row = $stmt->fetch()) {
                // Commencer une nouvelle session
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $row['username'];

                // Retourner au Javascript que la connexion est un succès
                $response['username'] = $row['username'];
                $response['message'] = 'Connexion réussie.';
            }
        } else {
            // Retourner au Javascript que la connexion a échoué.
            $error = "Nom d'utilisateur ou mot de passe invalide.";
            $response['error'] = true;
            $response['message'] = $error;
        }
    } catch (PDOException $e) {
        // Retourner au Javascript que le login n'a pas fonctionné
        $error = "Error: " . $e->getMessage();
        $response['error'] = true;
        $response['message'] = "Erreur: " . $e->getMessage();
    }

    // Retourner la réponse sous forme de JSON
    echo json_encode($response);
}
?>