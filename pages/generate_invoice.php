<?php
session_start(); // Start the session
require_once '../class/Database.php';
require_once '../vendor/autoload.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if reservation_id is provided
if (!isset($_GET['reservation_id']) || !is_numeric($_GET['reservation_id'])) {
    die('Invalid reservation ID.');
}

$reservation_id = intval($_GET['reservation_id']);
error_log("Attempting to generate invoice for reservation ID: " . $reservation_id);

// Connect to the database
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Check database connection
if ($conn->connect_error) {
    $response['message'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Fetch reservation details
$sql = "SELECT r.*, v.destination, v.aeroport_depart, v.aeroport_arrivee, v.date_depart, v.prix as vol_prix, 
        u.nom, u.prenom, u.email, c.adress, c.pays, c.num_passeport, c.date_naissance
        FROM reservation r
        JOIN vol v ON r.numero_vol = v.numero_vol
        JOIN utilisateur u ON r.client_id = u.id
        LEFT JOIN client c ON u.id = c.id
        WHERE r.id = ?";

error_log("SQL Query: " . $sql);
error_log("Reservation ID: " . $reservation_id);

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("Database error: " . $conn->error);
}

$stmt->bind_param('i', $reservation_id);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    die("Database error: " . $stmt->error);
}

$result = $stmt->get_result();
error_log("Number of rows found: " . $result->num_rows);

if ($result->num_rows === 0) {
    // Let's check if the reservation exists at all
    $check_sql = "SELECT * FROM reservation WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $reservation_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        error_log("Reservation ID {$reservation_id} not found in reservation table");
        die("Reservation not found in database.");
    } else {
        error_log("Reservation exists but join query failed. Checking related tables...");
        // Check if the related records exist
        $reservation = $check_result->fetch_assoc();
        error_log("Reservation details: " . print_r($reservation, true));
        
        // Check vol table
        $vol_sql = "SELECT * FROM vol WHERE numero_vol = ?";
        $vol_stmt = $conn->prepare($vol_sql);
        $vol_stmt->bind_param('s', $reservation['numero_vol']);
        $vol_stmt->execute();
        $vol_result = $vol_stmt->get_result();
        error_log("Vol records found: " . $vol_result->num_rows);
        
        // Check utilisateur table
        $user_sql = "SELECT * FROM utilisateur WHERE id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param('i', $reservation['client_id']);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        error_log("User records found: " . $user_result->num_rows);
        
        // Check client table
        $client_sql = "SELECT * FROM client WHERE id = ?";
        $client_stmt = $conn->prepare($client_sql);
        $client_stmt->bind_param('i', $reservation['client_id']);
        $client_stmt->execute();
        $client_result = $client_stmt->get_result();
        error_log("Client records found: " . $client_result->num_rows);

        die("Reservation exists but some related data is missing. Check error logs for details.");
    }
}

$reservation = $result->fetch_assoc();
error_log("Successfully fetched reservation data: " . print_r($reservation, true));

// Function to replace 'é' with 'e'
function clean_text($text) {
    return str_replace('é', 'e', $text);
}

// Create PDF - Use FPDF directly without namespace
$pdf = new FPDF();
$pdf->AddPage();

// Add logo to the top left
// Adjust the path and position as needed
$pdf->Image('../images/LOGO.png', 10, 10, 30); // Image(file, x, y, w)

// Add title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, clean_text('Facture de Reservation'), 0, 1, 'C');
$pdf->Ln(20); // Add more space after the title

// Add content to PDF
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240); // Light gray background for sections

// Client Details Section
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, clean_text('Details du Client'), 0, 1, 'L', true); // With background
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Reservation ID: {$reservation_id}", 0, 1);
$pdf->Cell(0, 8, clean_text("Client: {$reservation['nom']} {$reservation['prenom']}"), 0, 1);
$pdf->Cell(0, 8, clean_text("Email: {$reservation['email']}"), 0, 1);

// Display client-specific details only if they exist and are not empty
if (!empty($reservation['adress'])) {
    $pdf->Cell(0, 8, clean_text("Adresse: {$reservation['adress']}"), 0, 1);
}
if (!empty($reservation['pays'])) {
    $pdf->Cell(0, 8, clean_text("Pays: {$reservation['pays']}"), 0, 1);
}
if (!empty($reservation['num_passeport'])) {
    $pdf->Cell(0, 8, clean_text("Numero de passeport: {$reservation['num_passeport']}"), 0, 1);
}
if (!empty($reservation['date_naissance']) && $reservation['date_naissance'] !== '0000-00-00') {
    $pdf->Cell(0, 8, clean_text("Date de naissance: " . date('d/m/Y', strtotime($reservation['date_naissance']))), 0, 1);
}

$pdf->Ln(10);

// Flight Details Section
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Details du Vol', 0, 1, 'L', true); // With background
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Vol: {$reservation['numero_vol']}", 0, 1);
$pdf->Cell(0, 8, clean_text("Destination: {$reservation['destination']}"), 0, 1);
$pdf->Cell(0, 8, clean_text("Depart: {$reservation['aeroport_depart']}"), 0, 1);
$pdf->Cell(0, 8, clean_text("Arrivee: {$reservation['aeroport_arrivee']}"), 0, 1);
$pdf->Cell(0, 8, "Date de depart: " . date('d/m/Y H:i', strtotime($reservation['date_depart'])), 0, 1);
// Check if nombre_billets is set and not 0 before displaying
if (isset($reservation['nombre_billets']) && $reservation['nombre_billets'] > 0) {
    $pdf->Cell(0, 8, "Nombre de billets: {$reservation['nombre_billets']}", 0, 1);
}
$pdf->Ln(10);

// Hotel Details Section (only if hotel info exists)
if ($reservation['nom_hotel']) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Details de l'Hotel", 0, 1, 'L', true); // With background
    $pdf->Ln(2);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, clean_text("Hotel: {$reservation['nom_hotel']}"), 0, 1);
    // Check if nombre_chambres is set and not 0 before displaying
    if (isset($reservation['nombre_chambres']) && $reservation['nombre_chambres'] > 0) {
       $pdf->Cell(0, 8, "Nombre de chambres: {$reservation['nombre_chambres']}", 0, 1);
    }
    // Check if type_chambre is set and not empty before displaying
    if (isset($reservation['type_chambre']) && !empty($reservation['type_chambre'])) {
       $pdf->Cell(0, 8, clean_text("Type de chambre: {$reservation['type_chambre']}"), 0, 1);
    }
    // Check if hotel dates are set and not '0000-00-00' before displaying
    if (!empty($reservation['date_debut_hotel']) && $reservation['date_debut_hotel'] !== '0000-00-00') {
       $pdf->Cell(0, 8, "Date de debut: " . date('d/m/Y', strtotime($reservation['date_debut_hotel'])), 0, 1);
    }
    if (!empty($reservation['date_fin_hotel']) && $reservation['date_fin_hotel'] !== '0000-00-00') {
        $pdf->Cell(0, 8, "Date de fin: " . date('d/m/Y', strtotime($reservation['date_fin_hotel'])), 0, 1);
    }
    $pdf->Ln(10);
}

// Total Amount and Status
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, clean_text("Montant total: DZD " . number_format($reservation['montant_total'], 2, ',', ' ')), 0, 1, 'R'); // Align right
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Date d'emission: " . date('d/m/Y H:i'), 0, 1, 'R'); // Align right
$pdf->Cell(0, 8, clean_text("Statut: " . ($reservation['est_paye'] ? 'Paye' : 'Non paye')), 0, 1, 'R'); // Align right


// Output PDF
$pdf->Output('D', "Facture_Reservation_{$reservation_id}.pdf");
?> 