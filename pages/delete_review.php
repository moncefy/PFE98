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

    // Get the review ID from POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $review_id = $data['review_id'] ?? null;

    if (empty($review_id)) {
        throw new Exception("Review ID is required");
    }

    $client_id = $_SESSION['user_id'];

    // Verify that the review belongs to the logged-in client
    $sql_verify = "SELECT id FROM avis WHERE id = ? AND client_id = ?";
    $stmt_verify = $conn->prepare($sql_verify);
    if (!$stmt_verify) {
        throw new Exception("Prepare verify failed: " . $conn->error);
    }

    $stmt_verify->bind_param("ii", $review_id, $client_id);
    $stmt_verify->execute();
    $stmt_verify->store_result();

    if ($stmt_verify->num_rows === 0) {
        throw new Exception("Review not found or doesn't belong to user");
    }
    $stmt_verify->close();

    // Delete the review
    $sql_delete = "DELETE FROM avis WHERE id = ? AND client_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    if (!$stmt_delete) {
        throw new Exception("Prepare delete failed: " . $conn->error);
    }

    $stmt_delete->bind_param("ii", $review_id, $client_id);
    if (!$stmt_delete->execute()) {
        throw new Exception("Delete failed: " . $stmt_delete->error);
    }

    $stmt_delete->close();
    $conn->close();

    $response = ['success' => true, 'message' => 'Review deleted successfully!'];

} catch (Exception $e) {
    error_log("Error in delete_review.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => $e->getMessage()];
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?> 