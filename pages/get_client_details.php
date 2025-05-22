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

    $client_id = $_SESSION['user_id'];

    // Fetch client details from the 'utilisateur' table
    $sql = "SELECT prenom, nom, email, telephone FROM utilisateur WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    if ($client) {
        $response = ['success' => true, 'data' => $client];
    } else {
        $response = ['success' => false, 'message' => 'Client not found.'];
    }

} catch (Exception $e) {
    error_log("Error in get_client_details.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => $e->getMessage()];
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?> 