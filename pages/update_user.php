<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../class/Database.php';

// Get POST data (JSON body)
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields (user_id, prenom, nom, email, role_id)
$required_fields = ['user_id', 'prenom', 'nom', 'email', 'role_id'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
        exit();
    }
}

// Validate user_id is an integer
if (!filter_var($data['user_id'], FILTER_VALIDATE_INT)) {
     echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide.']);
    exit();
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format d\'email invalide']);
    exit();
}

// Validate role_id
$valid_roles = [1, 2, 3]; // 1: client, 2: gestionnaire, 3: admin
if (!in_array((int)$data['role_id'], $valid_roles)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide.']);
    exit();
}

$userId = (int)$data['user_id'];
$prenom = $data['prenom'];
$nom = $data['nom'];
$email = $data['email'];
$telephone = $data['telephone'] ?? null; // Telephone is optional
$roleId = (int)$data['role_id'];

try {
    // Initialize database connection
    $db = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    // Check if email already exists for another user
    $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ? AND id != ?");
    $stmt->bind_param('si', $email, $userId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre utilisateur.']);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    // Update utilisateur table
    // Note: We re-select role_name based on role_id to ensure consistency
    $stmt = $conn->prepare("
        UPDATE utilisateur 
        SET prenom = ?, nom = ?, email = ?, telephone = ?, role_id = ?, role_name = (SELECT nom FROM role WHERE id = ?) 
        WHERE id = ?
    ");

    $stmt->bind_param(
        'ssssiii',
        $prenom,
        $nom,
        $email,
        $telephone,
        $roleId,
        $roleId,
        $userId
    );

    if (!$stmt->execute()) {
        throw new Exception("Error updating user: " . $stmt->error);
    }

    // Note: If role_id changes, you might need additional logic here
    // to update/delete records in client, gestionnaire, or admin tables
    // depending on your specific triggers or constraints.
    // Our trigger should handle creation, but deletion/migration might need manual handling.
    // For now, assuming triggers handle this or it's not required.

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès.']);

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