<?php
require_once '../class/Database.php';

// Get JSON data from request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

try {
    // Connect to database
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Prepare update query
    $stmt = $conn->prepare("UPDATE vol SET 
        numero_vol = ?, 
        aeroport_depart = ?, 
        aeroport_arrivee = ?, 
        date_depart = ?, 
        compagnie_aerienne = ?, 
        prix = ? 
        WHERE id = ?");

    $stmt->bind_param(
        "sssssdi",
        $data['numero_vol'],
        $data['aeroport_depart'],
        $data['aeroport_arrivee'],
        $data['date_depart'],
        $data['compagnie_aerienne'],
        $data['prix'],
        $data['id']
    );

    // Execute update
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update flight']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 