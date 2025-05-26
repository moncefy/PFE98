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
    $stmt = $conn->prepare("UPDATE hotel SET 
        nom = ?, 
        adresse = ?, 
        ville = ?, 
        pays = ?, 
        etoiles = ?, 
        chambres_disponible = ?, 
        prix_par_nuit = ? 
        WHERE id = ?");

    $stmt->bind_param(
        "ssssiidi",
        $data['nom'],
        $data['adresse'],
        $data['ville'],
        $data['pays'],
        $data['etoiles'],
        $data['chambres_disponible'],
        $data['prix_par_nuit'],
        $data['id']
    );

    // Execute update
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update hotel']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 