<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../class/Database.php';

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get all hotels
    $stmt = $conn->prepare("SELECT id, nom, prix_par_nuit, chambres_disponible, ville, pays, etoiles FROM hotel");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Failed to get result: " . $stmt->error);
    }

    $hotels = $result->fetch_all(MYSQLI_ASSOC);

    // Return success response with hotels data
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $hotels
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Error in get_hotels.php: " . $e->getMessage());
    
    // Return error response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching hotels: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 