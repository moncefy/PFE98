<?php
session_start();

// Check if user is logged in and is a gestionnaire
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../class/Database.php';

// Get reservation ID from request
$reservation_id = $_GET['id'] ?? null;

error_log("Attempting to get details for reservation ID: " . $reservation_id);

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'Missing reservation ID']);
    exit();
}

// Initialize database connection
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Get reservation details with related information
$sql = "
    SELECT 
        r.*,
        u.nom as client_nom,
        u.prenom as client_prenom,
        u.email as client_email,
        u.telephone as client_telephone,
        v.numero_vol,
        v.compagnie_aerienne,
        v.aeroport_depart,
        v.aeroport_arrivee,
        v.destination,
        v.date_depart,
        v.date_arrivee,
        v.type_vol,
        h.nom as hotel_nom,
        h.etoiles as hotel_etoiles,
        h.ville as hotel_ville,
        h.pays as hotel_pays,
        r.date_debut_hotel as date_debut,
        r.date_fin_hotel as date_fin
    FROM reservation r
    JOIN client c ON r.client_id = c.id
    JOIN utilisateur u ON c.id = u.id
    JOIN vol v ON r.numero_vol = v.numero_vol
    LEFT JOIN hotel h ON r.nom_hotel = h.nom
    WHERE r.id = ?
";
error_log("SQL Query: " . $sql);
$stmt = $conn->prepare($sql);

$stmt->bind_param('i', $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

error_log("Query Result: " . print_r($reservation, true));

if (!$reservation) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
    exit();
}

// Format dates
$reservation['date_reservation'] = date('d/m/Y H:i', strtotime($reservation['date_reservation']));
$reservation['date_depart'] = date('d/m/Y H:i', strtotime($reservation['date_depart']));
$reservation['date_arrivee'] = date('d/m/Y H:i', strtotime($reservation['date_arrivee']));

// Handle potential null/empty hotel dates
$reservation['date_debut'] = !empty($reservation['date_debut']) ? date('d/m/Y', strtotime($reservation['date_debut'])) : '';
$reservation['date_fin'] = !empty($reservation['date_fin']) ? date('d/m/Y', strtotime($reservation['date_fin'])) : '';

// Format amounts
$reservation['montant_total'] = number_format($reservation['montant_total'], 2, ',', ' ');

echo json_encode(['success' => true, 'reservation' => $reservation]);
$conn->close(); 