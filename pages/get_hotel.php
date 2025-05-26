<?php
require_once '../class/Database.php';

// Get hotel ID from request
$hotelId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hotelId <= 0) {
    echo json_encode(['error' => 'Invalid hotel ID']);
    exit;
}

try {
    // Connect to database
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM hotel WHERE id = ?");
    $stmt->bind_param("i", $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($hotel = $result->fetch_assoc()) {
        echo json_encode($hotel);
    } else {
        echo json_encode(['error' => 'Hotel not found']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 