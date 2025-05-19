<?php
require_once '../class/Database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Check if JSON data was received and decoded successfully
    if ($data === null) {
        $response['message'] = 'Invalid JSON data.';
        echo json_encode($response);
        exit();
    }

    // Extract data, with basic validation
    $numero_vol = $data['numero_vol'] ?? null;
    $nom_hotel = $data['nom_hotel'] ?? null; // Can be null
    $nombre_chambres = $data['nombre_chambres'] ?? 0; // Default to 0 if no hotel
    $type_chambre = $data['type_chambre'] ?? null; // Can be null
    $montant_total = $data['montant_total'] ?? null;

    // Basic validation for required fields
    if (!$numero_vol || $montant_total === null) {
        $response['message'] = 'Missing required data (numero_vol, montant_total).';
        echo json_encode($response);
        exit();
    }

    // TODO: Implement actual client_id retrieval based on user sessionReservation Data: 
    // For now, using a placeholder. Replace with actual logic.
    $client_id = 1; // Placeholder: Replace with actual authenticated user ID

    // Connect to the database
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Check database connection
    if ($conn->connect_error) {
        $response['message'] = 'Database connection failed: ' . $conn->connect_error;
        echo json_encode($response);
        exit();
    }

    // Prepare SQL INSERT statement
    $sql = "INSERT INTO reservation (numero_vol, nom_hotel, nombre_chambres, type_chambre, client_id, date_reservation, statut, montant_total, est_paye) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

    $statut = 'En Cours'; // As requested
    $est_paye = 0; // As requested (Non payé), using 0 for tinyint(1)

    $stmt = $conn->prepare($sql);

    // Bind parameters
    // s: string, i: integer, d: double/float
    $stmt->bind_param('ssiisdsi', $numero_vol, $nom_hotel, $nombre_chambres, $type_chambre, $client_id, $statut, $montant_total, $est_paye);

    // Execute the statement
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Reservation saved successfully.';
    } else {
        $response['message'] = 'Error saving reservation: ' . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?> 