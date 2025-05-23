<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    error_log('Delete user: Unauthorized access attempt');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);
error_log('Delete user: Received data: ' . print_r($data, true));

if (!isset($data['user_id'])) {
    error_log('Delete user: Missing user_id in request');
    echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
    exit();
}

require_once '../class/Database.php';

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();
    error_log('Delete user: Database connection established');

    // Start transaction
    $conn->begin_transaction();
    error_log('Delete user: Transaction started');

    // Delete related records first (to maintain referential integrity)
    // Delete notifications
    $stmt = $conn->prepare("DELETE FROM notification WHERE utilisateur_id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted notifications for user ' . $data['user_id']);

    // Delete reservations
    $stmt = $conn->prepare("DELETE FROM reservation WHERE client_id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted reservations for user ' . $data['user_id']);

    // Delete client specific data if exists
    $stmt = $conn->prepare("DELETE FROM client WHERE id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted client data for user ' . $data['user_id']);

    // Delete gestionnaire specific data if exists
    $stmt = $conn->prepare("DELETE FROM gestionnaire WHERE id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted gestionnaire data for user ' . $data['user_id']);

    // Delete admin specific data if exists
    $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted admin data for user ' . $data['user_id']);

    // Finally, delete the user
    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
    $stmt->bind_param('i', $data['user_id']);
    $stmt->execute();
    error_log('Delete user: Deleted user ' . $data['user_id']);

    // If we got here, commit the transaction
    $conn->commit();
    error_log('Delete user: Transaction committed successfully');

    echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);

} catch (Exception $e) {
    // If there was an error, rollback the transaction
    if (isset($conn)) {
        $conn->rollback();
        error_log('Delete user: Transaction rolled back due to error: ' . $e->getMessage());
    }
    error_log('Delete user: Error occurred: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
} finally {
    // Close the connection
    if (isset($conn)) {
        $conn->close();
        error_log('Delete user: Database connection closed');
    }
} 