<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../class/Database.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log received data for debugging
error_log('Add User Received Data: ' . print_r($data, true));

// Validate required fields
$required_fields = ['prenom', 'nom', 'email', 'password', 'role_id'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
        exit();
    }
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format d\'email invalide']);
    exit();
}

// Validate role_id
$valid_roles = [1, 2, 3]; // 1: client, 2: gestionnaire, 3: admin
if (!in_array($data['role_id'], $valid_roles)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
    exit();
}

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
    $stmt->bind_param('s', $data['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    // Insert into utilisateur table
    $stmt = $conn->prepare("
        INSERT INTO utilisateur (prenom, nom, email, password, telephone, role_id, role_name)
        VALUES (?, ?, ?, ?, ?, ?, (SELECT nom FROM role WHERE id = ?))
    ");
    
    $stmt->bind_param(
        'sssssii',
        $data['prenom'],
        $data['nom'],
        $data['email'],
        $data['password'],
        $data['telephone'],
        $data['role_id'],
        $data['role_id']
    );

    if (!$stmt->execute()) {
        throw new Exception("Error creating user: " . $stmt->error);
    }

    $user_id = $conn->insert_id;

    // The trigger will automatically create the corresponding record in client/gestionnaire/admin table
    // based on the role_id

    // Now update the role-specific table with additional data
    if ($data['role_id'] == 1) { // Client
        error_log('Entering Client Role Update Block');
        $adress = $data['adress'] ?? null;
        $pays = $data['pays'] ?? null;
        $numPasseport = $data['num_passeport'] ?? null;
        $dateNaissance = $data['date_naissance'] ?? null;

        // Log client data for debugging
        error_log('Client Data for Update:');
        error_log('Adress: ' . $adress);
        error_log('Pays: ' . $pays);
        error_log('Num Passeport: ' . $numPasseport);
        error_log('Date Naissance: ' . $dateNaissance);
        error_log('User ID: ' . $user_id);

        $stmt = $conn->prepare("UPDATE client SET adress = ?, pays = ?, num_passeport = ?, date_naissance = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $adress, $pays, $numPasseport, $dateNaissance, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating client data: " . $stmt->error);
        }

    } elseif ($data['role_id'] == 2) { // Gestionnaire
        $poste = $data['poste'] ?? null;
        $departement = $data['departement'] ?? null;

        $stmt = $conn->prepare("UPDATE gestionnaire SET poste = ?, departement = ? WHERE id = ?");
        $stmt->bind_param('ssi', $poste, $departement, $user_id);
         if (!$stmt->execute()) {
            throw new Exception("Error updating gestionnaire data: " . $stmt->error);
        }
    }

    // Add update for admin table if role is 3
    if ($data['role_id'] == 3) { // Admin
        $niveau = $data['niveau'] ?? null;

        $stmt = $conn->prepare("UPDATE admin SET niveau = ? WHERE id = ?");
        $stmt->bind_param('si', $niveau, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating admin data: " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès']);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 