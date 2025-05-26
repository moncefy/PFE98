<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    header('Location: Login.php');
    exit();
}

require_once '../class/Database.php';

// Initialize database connection
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Get admin info
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT niveau FROM admin WHERE id = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Get system statistics
// Total users
$stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM utilisateur");
$stmt->execute();
$total_users = $stmt->get_result()->fetch_assoc()['total_users'];

// Total flights
$stmt = $conn->prepare("SELECT COUNT(*) as total_flights FROM vol");
$stmt->execute();
$total_flights = $stmt->get_result()->fetch_assoc()['total_flights'];

// Total hotels
$stmt = $conn->prepare("SELECT COUNT(*) as total_hotels FROM hotel");
$stmt->execute();
$total_hotels = $stmt->get_result()->fetch_assoc()['total_hotels'];

// Total reservations
$stmt = $conn->prepare("SELECT COUNT(*) as total_reservations FROM reservation");
$stmt->execute();
$total_reservations = $stmt->get_result()->fetch_assoc()['total_reservations'];

// Get recent users
$stmt = $conn->prepare("
    SELECT u.*, r.nom as role_name 
    FROM utilisateur u 
    JOIN role r ON u.role_id = r.id 
    ORDER BY u.date_creation DESC 
    LIMIT 5
");
$stmt->execute();
$recent_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get recent flights
$stmt = $conn->prepare("
    SELECT * FROM vol 
    ORDER BY date_depart DESC 
    LIMIT 5
");
$stmt->execute();
$recent_flights = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get recent hotels
$stmt = $conn->prepare("
    SELECT * FROM hotel 
    ORDER BY id DESC 
    LIMIT 5
");
$stmt->execute();
$recent_hotels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Agence de Voyage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="../images/LOGO.png" alt="Logo" class="h-12">
                    <span class="ml-4 text-xl font-semibold text-gray-800">Admin Dashboard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="notificationBell" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                    </div>
                    <button onclick="showLogsModal()" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-history text-xl"></i>
                        <span class="ml-2">Consulter Logs</span>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-700"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
                        <a href="logout.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?>!</h1>
            <p class="text-gray-600">
                Niveau d'administration: <?= htmlspecialchars($admin['niveau']) ?>
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Utilisateurs</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $total_users ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-plane text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Vols</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $total_flights ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-hotel text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Hôtels</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $total_hotels ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Réservations</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $total_reservations ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Users Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestion des Utilisateurs</h2>
                <div class="space-y-4">
                    <button onclick="showAddUserModal()" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
                        <i class="fas fa-user-plus mr-2"></i> Ajouter un Utilisateur
                    </button>
                    <button onclick="showUserList()" class="w-full bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition">
                        <i class="fas fa-list mr-2"></i> Liste des Utilisateurs
                    </button>
                </div>
            </div>

            <!-- Flights Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestion des Vols</h2>
                <div class="space-y-4">
                    <button onclick="showAddFlightModal()" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition">
                        <i class="fas fa-plane-departure mr-2"></i> Ajouter un Vol
                    </button>
                    <button onclick="showFlightList()" class="w-full bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition">
                        <i class="fas fa-list mr-2"></i> Liste des Vols
                    </button>
                </div>
            </div>

            <!-- Hotels Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestion des Hôtels</h2>
                <div class="space-y-4">
                    <button onclick="showAddHotelModal()" class="w-full bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition">
                        <i class="fas fa-hotel mr-2"></i> Ajouter un Hôtel
                    </button>
                    <button onclick="showHotelList()" class="w-full bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition">
                        <i class="fas fa-list mr-2"></i> Liste des Hôtels
                    </button>
                </div>
            </div>
        </div>

        <!-- User List Section -->
        <div id="userListSection" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Liste Complète des Utilisateurs</h2>
            <div id="userListContent" class="space-y-4">
                <!-- User list will be loaded here by JavaScript -->
            </div>
            <button onclick="hideUserList()" class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Fermer la liste</button>
        </div>

        <!-- Flight List Section -->
        <div id="flightListSection" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Liste Complète des Vols</h2>
            <div id="flightListContent" class="space-y-4">
                <!-- Flight list will be loaded here by JavaScript -->
            </div>
            <button onclick="hideFlightList()" class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Fermer la liste</button>
        </div>

        <!-- Hotel List Section -->
        <div id="hotelListSection" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Liste Complète des Hôtels</h2>
            <div id="hotelListContent" class="space-y-4">
                <!-- Hotel list will be loaded here by JavaScript -->
            </div>
            <button onclick="hideHotelList()" class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Fermer la liste</button>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Utilisateurs Récents</h2>
                <div class="space-y-4">
                    <?php foreach ($recent_users as $user): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?= htmlspecialchars($user['role_name']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Flights -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Vols Récents</h2>
                <div class="space-y-4">
                    <?php foreach ($recent_flights as $flight): ?>
                        <div class="p-3 bg-gray-50 rounded">
                            <p class="font-medium"><?= htmlspecialchars($flight['numero_vol']) ?></p>
                            <p class="text-sm text-gray-600">
                                <?= htmlspecialchars($flight['aeroport_depart'] . ' → ' . $flight['aeroport_arrivee']) ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($flight['date_depart'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Hotels -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Hôtels Récents</h2>
                <div class="space-y-4">
                    <?php foreach ($recent_hotels as $hotel): ?>
                        <div class="p-3 bg-gray-50 rounded">
                            <p class="font-medium"><?= htmlspecialchars($hotel['nom']) ?></p>
                            <p class="text-sm text-gray-600">
                                <?= htmlspecialchars($hotel['ville'] . ', ' . $hotel['pays']) ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <?= str_repeat('⭐', $hotel['etoiles']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="min-h-screen px-4 text-center">
            <div class="inline-block align-middle my-8 p-6 w-full max-w-md text-left bg-white rounded-lg shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter un Utilisateur</h3>
                <form id="addUserForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="telephone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rôle</label>
                        <select name="role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="1">Client</option>
                            <option value="2">Gestionnaire</option>
                            <option value="3">Admin</option>
                        </select>
                    </div>

                    <!-- Client Specific Fields (initially hidden) -->
                    <div id="client_fields" class="space-y-4 hidden">
                        <h4 class="text-md font-semibold text-gray-800 pt-4 border-t">Informations Client</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adresse</label>
                            <input type="text" name="adress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pays</label>
                            <input type="text" name="pays" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Numéro de Passeport</label>
                            <input type="text" name="num_passeport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de Naissance</label>
                            <input type="date" name="date_naissance" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Gestionnaire Specific Fields (initially hidden) -->
                    <div id="gestionnaire_fields" class="space-y-4 hidden">
                         <h4 class="text-md font-semibold text-gray-800 pt-4 border-t">Informations Gestionnaire</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Poste</label>
                            <input type="text" name="poste" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Département</label>
                            <input type="text" name="departement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Admin Specific Fields (initially hidden) -->
                    <div id="admin_fields" class="space-y-4 hidden">
                        <h4 class="text-md font-semibold text-gray-800 pt-4 border-t">Informations Admin</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Niveau d'Administration</label>
                            <input type="text" name="niveau" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="min-h-screen px-4 text-center">
            <div class="inline-block align-middle my-8 p-6 w-full max-w-md text-left bg-white rounded-lg shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier l'Utilisateur</h3>
                <form id="editUserForm" class="space-y-4">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" id="edit_prenom" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" id="edit_nom" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="edit_email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="telephone" id="edit_telephone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rôle</label>
                        <select name="role_id" id="edit_role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="1">Client</option>
                            <option value="2">Gestionnaire</option>
                            <option value="3">Admin</option>
                        </select>
                    </div>
                     <div class="flex justify-between items-center mt-6">
                        <button type="button" onclick="showConfirmDeleteModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Supprimer</button>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="min-h-screen px-4 text-center">
            <div class="inline-block align-middle my-8 p-6 w-full max-w-sm text-left bg-white rounded-lg shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la Suppression</h3>
                <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeConfirmDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Hotel Modal -->
    <div id="editHotelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Modifier l'hôtel</h3>
                <button onclick="closeEditHotelModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editHotelForm" class="space-y-4">
                <input type="hidden" id="editHotelId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'hôtel</label>
                    <input type="text" id="editHotelNom" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" id="editHotelAdresse" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" id="editHotelVille" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <input type="text" id="editHotelPays" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Étoiles</label>
                        <select id="editHotelEtoiles" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="1">1 ⭐</option>
                            <option value="2">2 ⭐</option>
                            <option value="3">3 ⭐</option>
                            <option value="4">4 ⭐</option>
                            <option value="5">5 ⭐</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chambres disponibles</label>
                        <input type="number" id="editHotelChambres" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix par nuit (DZD)</label>
                    <input type="number" id="editHotelPrix" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeEditHotelModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Flight Modal -->
    <div id="editFlightModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Modifier le vol</h3>
                <button onclick="closeEditFlightModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editFlightForm" class="space-y-4">
                <input type="hidden" id="editFlightId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de vol</label>
                    <input type="text" id="editFlightNumero" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aéroport de départ</label>
                        <input type="text" id="editFlightDepart" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aéroport d'arrivée</label>
                        <input type="text" id="editFlightArrivee" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de départ</label>
                    <input type="datetime-local" id="editFlightDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Compagnie aérienne</label>
                    <input type="text" id="editFlightCompagnie" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix (DZD)</label>
                    <input type="number" id="editFlightPrix" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeEditFlightModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Modal -->
    <div id="logsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Journal d'activité</h3>
                <button onclick="closeLogsModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="overflow-y-auto flex-grow">
                <div class="space-y-4" id="logsContent">
                    <!-- Logs will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Hotel Modal -->
    <div id="addHotelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Ajouter un Nouvel Hôtel</h3>
                <button onclick="closeAddHotelModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addHotelForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'hôtel</label>
                    <input type="text" id="hotelNom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" id="hotelAdresse" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" id="hotelVille" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <input type="text" id="hotelPays" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Étoiles</label>
                        <select id="hotelEtoiles" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="1">1 ⭐</option>
                            <option value="2">2 ⭐</option>
                            <option value="3">3 ⭐</option>
                            <option value="4">4 ⭐</option>
                            <option value="5">5 ⭐</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chambres disponibles</label>
                        <input type="number" id="hotelChambres" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix par nuit (DZD)</label>
                    <input type="number" id="hotelPrix" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeAddHotelModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Ajouter l'Hôtel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Flight Modal -->
    <div id="addFlightModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Ajouter un Nouveau Vol</h3>
                <button onclick="closeAddFlightModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addFlightForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de vol</label>
                        <input type="text" id="flightNumero" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Compagnie aérienne</label>
                        <input type="text" id="flightCompagnie" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aéroport de départ</label>
                        <input type="text" id="flightDepart" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aéroport d'arrivée</label>
                        <input type="text" id="flightArrivee" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                    <input type="text" id="flightDestination" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de départ</label>
                        <input type="datetime-local" id="flightDateDepart" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'arrivée</label>
                        <input type="datetime-local" id="flightDateArrivee" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de vol</label>
                        <select id="flightType" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="direct">Direct</option>
                            <option value="escale">Avec escale</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Places disponibles</label>
                        <input type="number" id="flightPlaces" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix (DZD)</label>
                    <input type="number" id="flightPrix" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeAddFlightModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentEditingUserId = null;

        // Modal functions
        function showAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
            // Reset form and show default fields
            document.getElementById('addUserForm').reset();
            document.getElementById('client_fields').classList.add('hidden');
            document.getElementById('gestionnaire_fields').classList.add('hidden');
            document.getElementById('admin_fields').classList.add('hidden');
            // Trigger the role change handler to show/hide fields based on initial selection
            handleRoleChange();
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }

        // Function to handle visibility of role-specific fields
        function handleRoleChange() {
            const roleId = document.querySelector('#addUserForm select[name="role_id"]').value;
            document.getElementById('client_fields').classList.add('hidden');
            document.getElementById('gestionnaire_fields').classList.add('hidden');
            document.getElementById('admin_fields').classList.add('hidden');

            if (roleId === '1') {
                document.getElementById('client_fields').classList.remove('hidden');
            } else if (roleId === '2') {
                document.getElementById('gestionnaire_fields').classList.remove('hidden');
            } else if (roleId === '3') {
                document.getElementById('admin_fields').classList.remove('hidden');
            }
        }

        // Add event listener to role select dropdown
        document.querySelector('#addUserForm select[name="role_id"]').addEventListener('change', handleRoleChange);

        function showAddFlightModal() {
            const modal = document.getElementById('addFlightModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddFlightModal() {
            const modal = document.getElementById('addFlightModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // Reset form
            document.getElementById('addFlightForm').reset();
        }

        // Handle form submission
        document.getElementById('addFlightForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                numero_vol: document.getElementById('flightNumero').value,
                compagnie_aerienne: document.getElementById('flightCompagnie').value,
                aeroport_depart: document.getElementById('flightDepart').value,
                aeroport_arrivee: document.getElementById('flightArrivee').value,
                destination: document.getElementById('flightDestination').value,
                date_depart: document.getElementById('flightDateDepart').value,
                date_arrivee: document.getElementById('flightDateArrivee').value,
                type_vol: document.getElementById('flightType').value,
                prix: document.getElementById('flightPrix').value,
                places_disponibles: document.getElementById('flightPlaces').value
            };

            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Vol ajouté avec succès!</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);

            // Close modal and reset form
            closeAddFlightModal();
        });

        function showAddHotelModal() {
            const modal = document.getElementById('addHotelModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddHotelModal() {
            const modal = document.getElementById('addHotelModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // Reset form
            document.getElementById('addHotelForm').reset();
        }

        // Handle hotel form submission
        document.getElementById('addHotelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                nom: document.getElementById('hotelNom').value,
                adresse: document.getElementById('hotelAdresse').value,
                ville: document.getElementById('hotelVille').value,
                pays: document.getElementById('hotelPays').value,
                etoiles: document.getElementById('hotelEtoiles').value,
                chambres_disponible: document.getElementById('hotelChambres').value,
                prix_par_nuit: document.getElementById('hotelPrix').value
            };

            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Hôtel ajouté avec succès!</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);

            // Close modal and reset form
            closeAddHotelModal();
        });

        function showUserList() {
            const userListSection = document.getElementById('userListSection');
            if (userListSection.classList.contains('hidden')) {
                userListSection.classList.remove('hidden');
                fetchUserList();
            } else {
                userListSection.classList.add('hidden');
            }
        }

        function hideUserList() {
            document.getElementById('userListSection').classList.add('hidden');
        }

        async function fetchUserList() {
            const userListContent = document.getElementById('userListContent');
            userListContent.innerHTML = '<p>Chargement de la liste des utilisateurs...</p>';

            try {
                const response = await fetch('get_users.php');
                const users = await response.json();

                if (users.success) {
                    userListContent.innerHTML = ''; // Clear loading message
                    if (users.data.length > 0) {
                        users.data.forEach(user => {
                            const userDiv = document.createElement('div');
                            userDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded';
                            userDiv.innerHTML = `
                                <div>
                                    <p class="font-medium">${escapeHTML(user.prenom)} ${escapeHTML(user.nom)}</p>
                                    <p class="text-sm text-gray-600">${escapeHTML(user.email)}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 mr-2">
                                        ${escapeHTML(user.role_name)}
                                    </span>
                                    <button onclick="showEditUserModal(${user.id})" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition mr-2">
                                        Modifier
                                    </button>
                                    <button onclick="deleteUser(${user.id})" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Supprimer
                                    </button>
                                </div>
                            `;
                            userListContent.appendChild(userDiv);
                        });
                    } else {
                        userListContent.innerHTML = '<p>Aucun utilisateur trouvé.</p>';
                    }
                } else {
                    userListContent.innerHTML = `<p>Erreur lors du chargement: ${users.message}</p>`;
                }

            } catch (error) {
                console.error('Error fetching users:', error);
                userListContent.innerHTML = '<p>Erreur lors du chargement de la liste des utilisateurs.</p>';
            }
        }

        // Basic HTML escaping for security
        function escapeHTML(str) {
            // Ensure input is a string before calling replace
            const s = String(str ?? '');
            return s.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }

        function showFlightList() {
            const flightListSection = document.getElementById('flightListSection');
            if (flightListSection.classList.contains('hidden')) {
                flightListSection.classList.remove('hidden');
                fetchFlightList();
            } else {
                flightListSection.classList.add('hidden');
            }
        }

        function hideFlightList() {
            document.getElementById('flightListSection').classList.add('hidden');
        }

        async function fetchFlightList() {
            const flightListContent = document.getElementById('flightListContent');
            flightListContent.innerHTML = '<p>Chargement de la liste des vols...</p>';

            try {
                console.log('Fetching flights...');
                const response = await fetch('get_flights.php');
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    flightListContent.innerHTML = ''; // Clear loading message
                    if (result.data && result.data.length > 0) {
                        console.log('Number of flights:', result.data.length);
                        result.data.forEach(flight => {
                            const flightDiv = document.createElement('div');
                            flightDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded';
                            
                            // Format the date
                            const departureDate = new Date(flight.date_depart);
                            const formattedDate = departureDate.toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            flightDiv.innerHTML = `
                                <div>
                                    <p class="font-medium">Vol ${escapeHTML(flight.numero_vol)}</p>
                                    <p class="text-sm text-gray-600">${escapeHTML(flight.aeroport_depart)} → ${escapeHTML(flight.aeroport_arrivee)}</p>
                                    <p class="text-sm text-gray-500">${formattedDate}</p>
                                    <p class="text-sm text-gray-500">${escapeHTML(flight.compagnie_aerienne) ?? ''}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 mr-2">
                                    Prix : DZD ${parseFloat(flight.prix).toLocaleString('fr-FR')}
                                        
                                    </span>
                                    <button onclick="showEditFlightModal(${flight.id})" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition mr-2">
                                        Modifier
                                    </button>
                                    <button onclick="deleteFlight(${flight.id})" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Supprimer
                                    </button>
                                </div>
                            `;
                            flightListContent.appendChild(flightDiv);
                        });
                    } else {
                        flightListContent.innerHTML = '<p class="text-gray-500 text-center py-4">Aucun vol trouvé.</p>';
                    }
                } else {
                    console.error('Error from server:', result.message);
                    flightListContent.innerHTML = `<p class="text-red-500 text-center py-4">Erreur: ${result.message}</p>`;
                }

            } catch (error) {
                console.error('Error fetching flights:', error);
                flightListContent.innerHTML = '<p class="text-red-500 text-center py-4">Erreur lors du chargement de la liste des vols.</p>';
            }
        }

        function showHotelList() {
            const hotelListSection = document.getElementById('hotelListSection');
            if (hotelListSection.classList.contains('hidden')) {
                hotelListSection.classList.remove('hidden');
                fetchHotelList();
            } else {
                hotelListSection.classList.add('hidden');
            }
        }

        function hideHotelList() {
            document.getElementById('hotelListSection').classList.add('hidden');
        }

        async function fetchHotelList() {
            const hotelListContent = document.getElementById('hotelListContent');
            hotelListContent.innerHTML = '<p>Chargement de la liste des hôtels...</p>';

            try {
                console.log('Fetching hotels...');
                const response = await fetch('get_hotels.php'); // We will create this file next
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    hotelListContent.innerHTML = ''; // Clear loading message
                    if (result.data && result.data.length > 0) {
                        console.log('Number of hotels:', result.data.length);
                        result.data.forEach(hotel => {
                            const hotelDiv = document.createElement('div');
                            hotelDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded';

                            hotelDiv.innerHTML = `
                                <div>
                                    <p class="font-medium">${escapeHTML(hotel.nom)} (${hotel.etoiles} ⭐)</p>
                                    <p class="text-sm text-gray-600">${escapeHTML(hotel.ville)}, ${escapeHTML(hotel.pays)}</p>
                                    <p class="text-sm text-gray-500">Chambres disponibles: ${hotel.chambres_disponible}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 mr-2">
                                        DZD ${parseFloat(hotel.prix_par_nuit).toLocaleString('fr-FR')}/nuit
                                    </span>
                                    <button onclick="showEditHotelModal(${hotel.id})" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition mr-2">
                                        Modifier
                                    </button>
                                    <button onclick="deleteHotel(${hotel.id})" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Supprimer
                                    </button>
                                </div>
                            `;
                            hotelListContent.appendChild(hotelDiv);
                        });
                    } else {
                        hotelListContent.innerHTML = '<p class="text-gray-500 text-center py-4">Aucun hôtel trouvé.</p>';
                    }
                } else {
                     console.error('Error from server:', result.message);
                     hotelListContent.innerHTML = `<p class="text-red-500 text-center py-4">Erreur: ${result.message}</p>`;
                }

            } catch (error) {
                console.error('Error fetching hotels:', error);
                hotelListContent.innerHTML = '<p class="text-red-500 text-center py-4">Erreur lors du chargement de la liste des hôtels.</p>';
            }
        }

        // Form submission
        document.getElementById('addUserForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Explicitly add role-specific data
            const roleId = data.role_id;
            if (roleId === '1') { // Client
                data.adress = document.querySelector('#addUserForm input[name="adress"]').value;
                data.pays = document.querySelector('#addUserForm input[name="pays"]').value;
                data.num_passeport = document.querySelector('#addUserForm input[name="num_passeport"]').value;
                data.date_naissance = document.querySelector('#addUserForm input[name="date_naissance"]').value;
            } else if (roleId === '2') { // Gestionnaire
                data.poste = document.querySelector('#addUserForm input[name="poste"]').value;
                data.departement = document.querySelector('#addUserForm input[name="departement"]').value;
            } else if (roleId === '3') { // Admin
                data.niveau = document.querySelector('#addUserForm input[name="niveau"]').value;
            }

            try {
                const response = await fetch('add_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    alert('Utilisateur ajouté avec succès');
                    closeAddUserModal();
                    location.reload();
                } else {
                    alert(result.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Une erreur est survenue');
            }
        });

        // Edit User Modal functions
        async function showEditUserModal(userId) {
            currentEditingUserId = userId;
            const editUserModal = document.getElementById('editUserModal');
            const editUserForm = document.getElementById('editUserForm');
            const editUserIdInput = document.getElementById('edit_user_id');
            const editPrenomInput = document.getElementById('edit_prenom');
            const editNomInput = document.getElementById('edit_nom');
            const editEmailInput = document.getElementById('edit_email');
            const editTelephoneInput = document.getElementById('edit_telephone');
            const editRoleIdSelect = document.getElementById('edit_role_id');

            // Clear previous data and show loading state
            editUserForm.reset();
            editUserIdInput.value = userId;

            try {
                const response = await fetch(`get_user.php?id=${userId}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const user = result.data;
                    editPrenomInput.value = user.prenom;
                    editNomInput.value = user.nom;
                    editEmailInput.value = user.email;
                    editTelephoneInput.value = user.telephone || '';
                    editRoleIdSelect.value = user.role_id;

                    editUserModal.classList.remove('hidden');
                } else {
                    alert(result.message || 'Erreur lors du chargement des données utilisateur.');
                }

            } catch (error) {
                console.error('Error fetching user data:', error);
                alert('Erreur lors du chargement des données utilisateur.');
            }
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        // Confirm Delete Modal functions
        function showConfirmDeleteModal() {
            document.getElementById('confirmDeleteModal').classList.remove('hidden');
        }

        function closeConfirmDeleteModal() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
        }

        function deleteUser(userId) {
            console.log('Delete user called with ID:', userId);
            currentEditingUserId = userId;
            showConfirmDeleteModal();
        }

        async function confirmDelete() {
            console.log('Confirm delete called with ID:', currentEditingUserId);
            if (currentEditingUserId === null) {
                console.log('Error: currentEditingUserId is null');
                return;
            }

            try {
                const response = await fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: currentEditingUserId })
                });

                console.log('Response status:', response.status);
                const result = await response.json();
                console.log('Delete response:', result);

                if (result.success) {
                    alert('Utilisateur supprimé avec succès');
                    closeConfirmDeleteModal();
                    fetchUserList(); // Refresh the user list
                } else {
                    alert(result.message || 'Erreur lors de la suppression de l\'utilisateur.');
                }

            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Erreur lors de la suppression de l\'utilisateur.');
            }
        }

        // Handle Edit User Form submission
        document.getElementById('editUserForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Ensure user_id is included
            if (!data.user_id) {
                alert('Erreur: ID utilisateur manquant.');
                return;
            }

            try {
                const response = await fetch('update_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Profil utilisateur mis à jour avec succès');
                    closeEditUserModal();
                    fetchUserList(); // Refresh the user list
                } else {
                    alert(result.message || 'Une erreur est survenue lors de la mise à jour.');
                }

            } catch (error) {
                console.error('Error updating user:', error);
                alert('Une erreur est survenue lors de la mise à jour.');
            }
        });

        // Function to show edit modal
        function showEditHotelModal(hotelId) {
            // Fetch hotel details
            fetch(`get_hotel.php?id=${hotelId}`)
                .then(response => response.json())
                .then(hotel => {
                    // Fill the form with hotel details
                    document.getElementById('editHotelId').value = hotel.id;
                    document.getElementById('editHotelNom').value = hotel.nom;
                    document.getElementById('editHotelAdresse').value = hotel.adress;
                    document.getElementById('editHotelVille').value = hotel.ville;
                    document.getElementById('editHotelPays').value = hotel.pays;
                    document.getElementById('editHotelEtoiles').value = hotel.etoiles;
                    document.getElementById('editHotelChambres').value = hotel.chambres_disponible;
                    document.getElementById('editHotelPrix').value = hotel.prix_par_nuit;

                    // Show the modal
                    document.getElementById('editHotelModal').classList.remove('hidden');
                    document.getElementById('editHotelModal').classList.add('flex');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des détails de l\'hôtel');
                });
        }

        // Function to close edit modal
        function closeEditHotelModal() {
            document.getElementById('editHotelModal').classList.add('hidden');
            document.getElementById('editHotelModal').classList.remove('flex');
        }

        // Handle form submission
        document.getElementById('editHotelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('editHotelId').value,
                nom: document.getElementById('editHotelNom').value,
                adress: document.getElementById('editHotelAdresse').value,
                ville: document.getElementById('editHotelVille').value,
                pays: document.getElementById('editHotelPays').value,
                etoiles: document.getElementById('editHotelEtoiles').value,
                chambres_disponible: document.getElementById('editHotelChambres').value,
                prix_par_nuit: document.getElementById('editHotelPrix').value
            };

            // Send update request
            fetch('update_hotel.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Hôtel modifié avec succès!</span>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);

                    // Close modal and refresh hotel list
                    closeEditHotelModal();
                    loadHotels(); // Refresh the hotel list
                } else {
                    alert('Erreur lors de la modification de l\'hôtel');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la modification de l\'hôtel');
            });
        });

        // Function to show edit flight modal
        function showEditFlightModal(flightId) {
            // Fetch flight details
            fetch(`get_flight.php?id=${flightId}`)
                .then(response => response.json())
                .then(flight => {
                    // Fill the form with flight details
                    document.getElementById('editFlightId').value = flight.id;
                    document.getElementById('editFlightNumero').value = flight.numero_vol;
                    document.getElementById('editFlightDepart').value = flight.aeroport_depart;
                    document.getElementById('editFlightArrivee').value = flight.aeroport_arrivee;
                    
                    // Format date for datetime-local input
                    const departureDate = new Date(flight.date_depart);
                    const formattedDate = departureDate.toISOString().slice(0, 16);
                    document.getElementById('editFlightDate').value = formattedDate;
                    
                    document.getElementById('editFlightCompagnie').value = flight.compagnie_aerienne;
                    document.getElementById('editFlightPrix').value = flight.prix;

                    // Show the modal
                    document.getElementById('editFlightModal').classList.remove('hidden');
                    document.getElementById('editFlightModal').classList.add('flex');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des détails du vol');
                });
        }

        // Function to close edit flight modal
        function closeEditFlightModal() {
            document.getElementById('editFlightModal').classList.add('hidden');
            document.getElementById('editFlightModal').classList.remove('flex');
        }

        // Handle flight form submission
        document.getElementById('editFlightForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('editFlightId').value,
                numero_vol: document.getElementById('editFlightNumero').value,
                aeroport_depart: document.getElementById('editFlightDepart').value,
                aeroport_arrivee: document.getElementById('editFlightArrivee').value,
                date_depart: document.getElementById('editFlightDate').value,
                compagnie_aerienne: document.getElementById('editFlightCompagnie').value,
                prix: document.getElementById('editFlightPrix').value
            };

            // Send update request
            fetch('update_flight.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Vol modifié avec succès!</span>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);

                    // Close modal and refresh flight list
                    closeEditFlightModal();
                    fetchFlightList(); // Refresh the flight list
                } else {
                    alert('Erreur lors de la modification du vol');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la modification du vol');
            });
        });

        // Sample logs data
        const sampleLogs = [
            {
                type: 'payment',
                message: 'Client #23 a effectué un paiement',
                timestamp: '2025-05-24 14:30:00',
                icon: 'fa-money-bill-wave',
                color: 'bg-green-100 text-green-800'
            },
            {
                type: 'reservation',
                message: 'Client #54 a confirmé la réservation #68',
                timestamp: '2025-05-23 15:45:00',
                icon: 'fa-calendar-check',
                color: 'bg-blue-100 text-blue-800'
            },
            {
                type: 'rejection',
                message: 'Le gestionnaire #40 a rejeté la réservation #29 du client #68',
                timestamp: '2025-05-22 11:20:00',
                icon: 'fa-times-circle',
                color: 'bg-red-100 text-red-800'
            },
            {
                type: 'payment',
                message: 'Client #45 a effectué un paiement',
                timestamp: '2025-05-21 16:15:00',
                icon: 'fa-money-bill-wave',
                color: 'bg-green-100 text-green-800'
            },
            {
                type: 'reservation',
                message: 'Client #12 a confirmé la réservation #45',
                timestamp: '2025-05-20 09:30:00',
                icon: 'fa-calendar-check',
                color: 'bg-blue-100 text-blue-800'
            },
            {
                type: 'rejection',
                message: 'Le gestionnaire #33 a rejeté la réservation #12 du client #89',
                timestamp: '2025-05-20 08:45:00',
                icon: 'fa-times-circle',
                color: 'bg-red-100 text-red-800'
            }
        ];

        function showLogsModal() {
            const modal = document.getElementById('logsModal');
            const logsContent = document.getElementById('logsContent');
            
            // Clear previous logs
            logsContent.innerHTML = '';
            
            // Add logs with animation
            sampleLogs.forEach((log, index) => {
                const logElement = document.createElement('div');
                logElement.className = `p-4 rounded-lg ${log.color} transform transition-all duration-500 opacity-0 translate-y-4`;
                logElement.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${log.icon} mr-3"></i>
                        <div class="flex-grow">
                            <p class="font-medium">${log.message}</p>
                            <p class="text-sm opacity-75">${log.timestamp}</p>
                        </div>
                    </div>
                `;
                logsContent.appendChild(logElement);
                
                // Trigger animation
                setTimeout(() => {
                    logElement.classList.remove('opacity-0', 'translate-y-4');
                }, index * 100);
            });
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeLogsModal() {
            const modal = document.getElementById('logsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html> 