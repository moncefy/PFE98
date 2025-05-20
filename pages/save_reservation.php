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
    $nom_hotel = $data['nom_hotel'] ?? null;
    $nombre_chambres = $data['nombre_chambres'] ?? 0;
    $type_chambre = $data['type_chambre'] ?? null;
    $date_debut_hotel = $data['date_debut_hotel'] ?? null;
    $date_fin_hotel = $data['date_fin_hotel'] ?? null;
    $montant_total = $data['montant_total'] ?? null;

    // Basic validation for required fields
    if (!$numero_vol || $montant_total === null) {
        $response['message'] = 'Missing required data (numero_vol, montant_total).';
        echo json_encode($response);
        exit();
    }

    // TODO: Implement actual client_id retrieval based on user session
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
    $sql = "INSERT INTO reservation (numero_vol, nom_hotel, nombre_chambres, type_chambre, client_id, date_debut_hotel, date_fin_hotel, date_reservation, statut, montant_total, est_paye) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

    $statut = 'En Attente'; // As requested
    $est_paye = 0; // As requested (Non payé), using 0 for tinyint(1)

    $stmt = $conn->prepare($sql);

    // Bind parameters
    // s: string, i: integer, d: double/float
    $types = [
        's', // numero_vol
        's', // nom_hotel
        'i', // nombre_chambres
        's', // type_chambre
        'i', // client_id
        's', // date_debut_hotel
        's', // date_fin_hotel
        's', // statut
        'd', // montant_total
        'i'  // est_paye
    ];
    $stmt->bind_param(implode('', $types), $numero_vol, $nom_hotel, $nombre_chambres, $type_chambre, $client_id, $date_debut_hotel, $date_fin_hotel, $statut, $montant_total, $est_paye);

    // Execute the statement
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Reservation saved successfully.';
    } else {
        error_log("Database error: " . $stmt->error);
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