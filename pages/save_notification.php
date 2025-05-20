<?php
require_once '../class/Database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log("Received notification request: " . file_get_contents('php://input'));

// Connexion à la base
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the decoded data
error_log("Decoded data: " . print_r($data, true));

// Get user ID from session
session_start();
$user_id = $_SESSION['user_id'] ?? null;

// Log the user ID being used for insertion
error_log("Inserting notification for user ID from session: " . $user_id);

if (!$user_id || !isset($data['message'])) {
    error_log("Missing required data (user ID from session or message)");
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    // Insert notification
    $sql = "INSERT INTO notification (utilisateur_id, message, date_envoi) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $user_id, $data['message']);
    $success = $stmt->execute();

    // Log the result
    error_log("Notification insert result: " . ($success ? "Success" : "Failed"));

    if (!$success) {
        error_log("MySQL Error: " . $stmt->error);
    }

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    error_log("Exception in save_notification.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close(); 