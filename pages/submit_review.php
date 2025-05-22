<?php
session_start();

header('Content-Type: application/json');

$response = [];

// Enable error logging
error_log("Starting review submission process");

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    error_log("User not logged in or not a client. Session: " . print_r($_SESSION, true));
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

    // Get the posted data
    $raw_data = file_get_contents('php://input');
    error_log("Received data: " . $raw_data);
    $data = json_decode($raw_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data: " . json_last_error_msg());
    }

    $reservation_id = $data['reservation_id'] ?? null;
    $stars = $data['rating'] ?? null;
    $commentaire = $data['comment'] ?? null;

    error_log("Parsed data - reservation_id: $reservation_id, stars: $stars, commentaire: $commentaire");

    // Validate input
    if (empty($reservation_id) || !isset($stars) || empty($commentaire)) {
        throw new Exception("Missing required fields");
    }

    // Ensure stars is an integer between 1 and 5
    $stars = intval($stars);
    if ($stars < 1 || $stars > 5) {
        throw new Exception("Invalid stars value: $stars");
    }

    $client_id = $_SESSION['user_id'];
    error_log("Client ID: $client_id");

    // Verify that the reservation belongs to the logged-in client
    $sql_verify = "SELECT id FROM reservation WHERE id = ? AND client_id = ?";
    $stmt_verify = $conn->prepare($sql_verify);
    if (!$stmt_verify) {
        throw new Exception("Prepare verify failed: " . $conn->error);
    }

    $stmt_verify->bind_param("ii", $reservation_id, $client_id);
    $stmt_verify->execute();
    $stmt_verify->store_result();

    if ($stmt_verify->num_rows === 0) {
        throw new Exception("Reservation not found or doesn't belong to user");
    }
    $stmt_verify->close();

    // Check if a review already exists for this reservation
    $sql_check_review = "SELECT id FROM avis WHERE reservation_id = ?";
    $stmt_check_review = $conn->prepare($sql_check_review);
    if (!$stmt_check_review) {
        throw new Exception("Prepare check review failed: " . $conn->error);
    }

    $stmt_check_review->bind_param("i", $reservation_id);
    $stmt_check_review->execute();
    $stmt_check_review->store_result();

    if ($stmt_check_review->num_rows > 0) {
        throw new Exception("A review for this reservation already exists");
    }
    $stmt_check_review->close();

    // Insert the review into the avis table
    $sql_insert = "INSERT INTO avis (reservation_id, client_id, stars, commentaire) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) {
        throw new Exception("Prepare insert failed: " . $conn->error);
    }

    $stmt_insert->bind_param("iiis", $reservation_id, $client_id, $stars, $commentaire);
    if (!$stmt_insert->execute()) {
        throw new Exception("Insert failed: " . $stmt_insert->error);
    }

    $stmt_insert->close();
    $conn->close();

    $response = ['success' => true, 'message' => 'Review submitted successfully!'];
    error_log("Review submitted successfully");

} catch (Exception $e) {
    error_log("Error in submit_review.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => $e->getMessage()];
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?> 