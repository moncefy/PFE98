<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID vol manquant']);
    exit();
}

require_once '../class/Database.php';

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Get the flight
    $stmt = $conn->prepare("SELECT * FROM vol WHERE id = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();

    if ($flight) {
        echo json_encode([
            'success' => true,
            'data' => $flight
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Vol non trouvé'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération du vol: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 