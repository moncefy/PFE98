<?php
session_start();

header('Content-Type: application/json');

$response = [];

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    $response = ['success' => false, 'message' => 'Unauthorized access.'];
    echo json_encode($response);
    exit();
}

// Include database connection
require_once '../class/Database.php';

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get the updated data from POST request
    $data = json_decode(file_get_contents('php://input'), true);
    $prenom = $data['prenom'] ?? null;
    $nom = $data['nom'] ?? null;
    $email = $data['email'] ?? null;
    $telephone = $data['telephone'] ?? null;

    // Basic validation (you might want more robust validation)
    if (empty($prenom) || empty($nom) || empty($email)) {
        throw new Exception("Required fields (Prénom, Nom, Email) are missing.");
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    $client_id = $_SESSION['user_id'];

    // Update client details in the 'utilisateur' table
    $sql = "UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, telephone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // 'sissi' - string, string, integer, string, integer
    // We need to handle the case where telephone might be null or empty
    // Let's use 'ssssi' and ensure telephone is a string (empty string if null)
    $telephone_str = $telephone ?? ''; // Treat null telephone as empty string

    $stmt->bind_param("ssssi", $prenom, $nom, $email, $telephone_str, $client_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Update session variables if needed (e.g., prenom/nom displayed in header)
    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom'] = $nom;

    $response = ['success' => true, 'message' => 'Profile updated successfully!'];

} catch (Exception $e) {
    error_log("Error in update_client_profile.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => $e->getMessage()];
    if (isset($conn)) {
        $conn->close();
    } else {

    }
}

echo json_encode($response);
?> 