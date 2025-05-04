<?php
session_start();
require_once __DIR__ . '/../class/Database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');

    // Vérification simple
    if (!$nom || !$prenom || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password || !$telephone) {
        $errors[] = "Tous les champs sont requis et doivent être valides.";
    }

    if (empty($errors)) {
        $db   = new Database('localhost', 'root', '', 'your_database_name');
        $conn = $db->getConnection();

        // Vérifier si l'email existe déjà
        $check = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        $check->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, password, telephone, date_creation, role_id, role_name) VALUES (?, ?, ?, ?, ?, NOW(), 1, 'client')");
        $stmt->bind_param('sssss', $nom, $prenom, $email, $password, $telephone);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();

            // Insertion dans la table client
            $stmt2 = $conn->prepare("INSERT INTO client (utilisateur_id) VALUES (?)");
            $stmt2->bind_param('i', $user_id);
            if ($stmt2->execute()) {
                $success = true;
            } else {
                $errors[] = "Erreur lors de l'ajout dans la table client.";
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
                <label for="telephone" class="block text-gray-700">Téléphone</label>
                <input type="text" name="telephone" id="telephone" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">S'inscrire</button>
        </form>

        <div class="mt-4 text-center text-sm">
            Vous avez déjà un compte ? <a href="login.php" class="text-indigo-600 hover:underline">Connectez-vous</a>
        </div>
    </div>
</body>
</html>
