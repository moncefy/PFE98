<?php
session_start();
require_once __DIR__ . '/../class/Database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom            = trim($_POST['nom'] ?? '');
    $prenom         = trim($_POST['prenom'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $confirmPass    = $_POST['confirm_password'] ?? '';
    $telephone      = trim($_POST['telephone'] ?? '');
    $adresse        = trim($_POST['adresse'] ?? '');
    $pays           = trim($_POST['pays'] ?? '');
    $num_passeport  = trim($_POST['num_passeport'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';

    // Validations
    if (!$nom || !$prenom || !$email || !$password || !$confirmPass || !$telephone || !$adresse || !$pays || !$num_passeport || !$date_naissance) {
        $errors[] = "Tous les champs sont requis.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if ($password !== $confirmPass) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre et un chiffre.";
    }

    if (!preg_match('/^0[5-7][0-9]{8}$/', $telephone)) {
        $errors[] = "Le numéro de téléphone doit commencer par 05, 06 ou 07 et contenir exactement 10 chiffres.";
    }

    // Vérification de l'âge
    $date = DateTime::createFromFormat('Y-m-d', $date_naissance);
    if (!$date) {
        $errors[] = "Date de naissance invalide.";
    } else {
        $today = new DateTime();
        $age = $today->diff($date)->y;
        if ($age < 18) {
            $errors[] = "Vous devez avoir au moins 18 ans pour vous inscrire.";
        }
    }

    if (empty($errors)) {
        $db = new Database('localhost', 'root', '', 'pfe');
        $conn = $db->getConnection();

        // Vérifier si l'email existe déjà
        $check = $conn->prepare("SELECT email FROM utilisateur WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        $check->close();

        // Vérifier si le numéro de téléphone existe déjà
        $checkPhone = $conn->prepare("SELECT telephone FROM utilisateur WHERE telephone = ?");
        $checkPhone->bind_param('s', $telephone);
        $checkPhone->execute();
        $checkPhone->store_result();
        if ($checkPhone->num_rows > 0) {
            $errors[] = "Ce numéro de téléphone est déjà utilisé.";
        }
        $checkPhone->close();

        // Vérifier si le passeport est unique
        $checkPassport = $conn->prepare("SELECT num_passeport FROM client WHERE num_passeport = ?");
        $checkPassport->bind_param('s', $num_passeport);
        $checkPassport->execute();
        $checkPassport->store_result();
        if ($checkPassport->num_rows > 0) {
            $errors[] = "Ce numéro de passeport est déjà utilisé.";
        }
        $checkPassport->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, password, telephone, date_creation,role_id,role_name) VALUES (?, ?, ?, ?, ?, NOW(),1,'Client')");
        $stmt->bind_param('sssss', $nom, $prenom, $email, $hash, $telephone);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();

            // Insertion dans la table client
            $stmt2 = $conn->prepare("UPDATE client SET adress = ?, pays = ?, num_passeport = ?, date_naissance = ? WHERE id = ?");
            
            // Log values being prepared for client insertion
            error_log("Preparing client UPDATE with user_id: " . $user_id);
            error_log("  adresse: " . $adresse);
            error_log("  pays: " . $pays);
            error_log("  num_passeport: " . $num_passeport);
            error_log("  date_naissance: " . $date_naissance);

            $stmt2->bind_param('ssssi', $adresse, $pays, $num_passeport, $date_naissance, $user_id);
            try {
                if ($stmt2->execute()) {
                    $success = true;
                } else {
                    // Log the specific MySQL error if possible, but avoid showing sensitive details
                    error_log("MySQL execute error during client UPDATE for user_id " . $user_id . ": " . $stmt2->error);
                    // If client UPDATE fails, attempt to delete the user from utilisateur table
                    $deleteStmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
                    $deleteStmt->bind_param('i', $user_id);
                    if ($deleteStmt->execute()) {
                        error_log("Successfully deleted user with ID " . $user_id . " due to client UPDATE failure.");
                    } else {
                        error_log("Failed to delete user with ID " . $user_id . " after client UPDATE failure: " . $deleteStmt->error);
                    }
                    $deleteStmt->close();
                    $errors[] = "Erreur lors de l'enregistrement des informations client. Veuillez réessayer.";
                }
            } catch (mysqli_sql_exception $e) {
                // Catch specific SQL exceptions, like duplicate entry on primary key
                if ($e->getCode() == 1062) { // 1062 is the error code for duplicate entry
                     error_log("Duplicate entry error during client UPDATE for user_id " . $user_id . ": " . $e->getMessage());
                     error_log("  Attempted adresse: " . $adresse);
                     error_log("  Attempted pays: " . $pays);
                     error_log("  Attempted num_passeport: " . $num_passeport);
                     error_log("  Attempted date_naissance: " . $date_naissance);
                    // This might happen if the auto-increment ID in 'utilisateur' is already used in 'client'
                    $errors[] = "Une erreur système est survenue (ID client déjà utilisé). Veuillez contacter l'administrateur.";
                } else {
                    // Handle other SQL exceptions
                    error_log("Unexpected MySQL error during client UPDATE for user_id " . $user_id . ": " . $e->getMessage());
                    // Attempt to delete the user from utilisateur table for other SQL errors
                    $deleteStmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
                    $deleteStmt->bind_param('i', $user_id);
                    if ($deleteStmt->execute()) {
                        error_log("Successfully deleted user with ID " . $user_id . " due to unexpected client UPDATE error.");
                    } else {
                        error_log("Failed to delete user with ID " . $user_id . " after unexpected client UPDATE error: " . $deleteStmt->error);
                    }
                    $deleteStmt->close();
                    $errors[] = "Une erreur de base de données est survenue. Veuillez réessayer.";
                }
            }
            $stmt2->close();
        } else {
            $errors[] = "Erreur lors de l'inscription.";
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Agence de Voyage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
<div class="bg-white p-8 rounded-xl shadow-lg max-w-lg w-full">
    <h2 class="text-2xl font-bold text-center mb-6">Créer un compte client</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
        </div>
    <?php elseif ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            Inscription réussie ! <a href="login.php" class="text-indigo-600 underline">Connectez-vous ici</a>.
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label for="nom" class="block text-gray-700">Nom</label>
            <input type="text" name="nom" id="nom" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        </div>
        <div>
            <label for="prenom" class="block text-gray-700">Prénom</label>
            <input type="text" name="prenom" id="prenom" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div>
            <label for="password" class="block text-gray-700">Mot de passe</label>
            <input type="password" name="password" id="password" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
            <label for="confirm_password" class="block text-gray-700">Confirmer le mot de passe</label>
            <input type="password" name="confirm_password" id="confirm_password" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
            <label for="telephone" class="block text-gray-700">Téléphone</label>
            <input type="text" name="telephone" id="telephone" required maxlength="10" pattern="0[5-7][0-9]{8}" inputmode="numeric" class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
        </div>
        <div>
            <label for="adresse" class="block text-gray-700">Adresse</label>
            <input type="text" name="adresse" id="adresse" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
        </div>
        <div>
            <label for="pays" class="block text-gray-700">Pays</label>
            <input type="text" name="pays" id="pays" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['pays'] ?? '') ?>">
        </div>
        <div>
            <label for="num_passeport" class="block text-gray-700">Numéro de passeport</label>
            <input type="text" name="num_passeport" id="num_passeport" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['num_passeport'] ?? '') ?>">
        </div>
        <div>
            <label for="date_naissance" class="block text-gray-700">Date de naissance</label>
            <input type="date" name="date_naissance" id="date_naissance" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">S'inscrire</button>
    </form>

    <div class="mt-4 text-center text-sm">
        Vous avez déjà un compte ? <a href="login.php" class="text-indigo-600 hover:underline">Connectez-vous</a>
    </div>
</div>
</body>
</html>