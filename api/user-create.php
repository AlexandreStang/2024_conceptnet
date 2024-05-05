<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

include_once '../server/config/database.php';

$database = new Database();
$db = $database->getConnection();
$response = array("error" => false);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier que le nom d'utilisateur et le mot de passe sont bien inclus
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        try {
            // Définir et préparer la requête MySQL
            $query = "INSERT INTO users (username, password) VALUES (:username, :password);";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                // Insertion réussie
                $response['username'] = $username; // Use the input username directly
                $response['message'] = 'Utilisateur créé avec succès.';
            } else {
                // Aucune ligne affectée, aucune insertion effectuée
                $response['error'] = true;
                $response['message'] = "Aucun utilisateur n'a été créé.";
            }

        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
            $response['error'] = true;
            $response['message'] = "Error: " . $e->getMessage();
        }

        // Renvoie la réponse JSON
        echo json_encode($response);
    } else {
        $response['error'] = true;
        $response['message'] = "Le nom d'utilisateur ou le mot de passe est manquant.";
        echo json_encode($response);
        exit;
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Méthode de requête invalide.';
}
?>