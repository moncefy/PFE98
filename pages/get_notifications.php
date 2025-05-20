<?php
require_once '../class/Database.php';

// Connexion à la base
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Get user ID from session
session_start();
$user_id = $_SESSION['user_id'] ?? null;

// Log the user ID being used
error_log("Fetching notifications for user ID: " . $user_id);

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Fetch notifications
$sql = "SELECT id, message, date_envoi 
        FROM notification 
        WHERE utilisateur_id = ? 
        ORDER BY date_envoi DESC 
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Format dates
foreach ($notifications as &$notification) {
    $notification['date_envoi'] = date('d/m/Y H:i', strtotime($notification['date_envoi']));
}

echo json_encode(['success' => true, 'notifications' => $notifications]);
$conn->close(); 