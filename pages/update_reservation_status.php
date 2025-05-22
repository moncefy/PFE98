<?php
session_start();

// Check if user is logged in and is a gestionnaire
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../class/Database.php';

// Get request data
$reservation_id = $_POST['id'] ?? null;
$new_status = $_POST['status'] ?? null;

if (!$reservation_id || !$new_status) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

// Validate status
$valid_statuses = ['Confirmée', 'Rejetée'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Initialize database connection
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Start transaction
$conn->begin_transaction();

try {
    // Get reservation details for notification
    $stmt = $conn->prepare("
        SELECT r.*, c.id as client_id, v.numero_vol, v.destination
        FROM reservation r
        JOIN client c ON r.client_id = c.id
        JOIN vol v ON r.numero_vol = v.numero_vol
        WHERE r.id = ?
    ");
    $stmt->bind_param('i', $reservation_id);
    $stmt->execute();
    $reservation = $stmt->get_result()->fetch_assoc();

    if (!$reservation) {
        throw new Exception('Reservation not found');
    }

    // Update reservation status
    $stmt = $conn->prepare("UPDATE reservation SET statut = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $reservation_id);
    $stmt->execute();

    // Create notification for client
    $message = $new_status === 'Confirmée' 
        ? "Votre réservation pour le vol {$reservation['numero_vol']} vers {$reservation['destination']} a été confirmée."
        : "Votre réservation pour le vol {$reservation['numero_vol']} vers {$reservation['destination']} a été rejetée.";

    $stmt = $conn->prepare("
        INSERT INTO notification (utilisateur_id, message, date_envoi)
        VALUES (?, ?, NOW())
    ");
    $stmt->bind_param('is', $reservation['client_id'], $message);
    $stmt->execute();

    // If confirmed, update available seats
    if ($new_status === 'Confirmée') {
        $stmt = $conn->prepare("
            UPDATE vol 
            SET places_disponibles = places_disponibles - 1
            WHERE numero_vol = ?
        ");
        $stmt->bind_param('s', $reservation['numero_vol']);
        $stmt->execute();

        // If hotel is booked, update available rooms
        if ($reservation['nom_hotel']) {
            $stmt = $conn->prepare("
                UPDATE hotel 
                SET chambres_disponible = chambres_disponible - ?
                WHERE nom = ?
            ");
            $stmt->bind_param('is', $reservation['nombre_chambres'], $reservation['nom_hotel']);
            $stmt->execute();
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Reservation status updated successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close(); 