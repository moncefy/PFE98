<?php
session_start();

// Check if user is logged in and is a gestionnaire
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: Login.php');
    exit();
}

require_once '../class/Database.php';

// Initialize database connection
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Get gestionnaire info
$gestionnaire_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT poste, departement FROM gestionnaire WHERE id = ?");
$stmt->bind_param('i', $gestionnaire_id);
$stmt->execute();
$gestionnaire = $stmt->get_result()->fetch_assoc();

// Get pending reservations
$stmt = $conn->prepare("
    SELECT r.*, u.nom as client_nom, u.prenom as client_prenom, v.numero_vol, v.destination
    FROM reservation r
    JOIN client c ON r.client_id = c.id
    JOIN utilisateur u ON c.id = u.id
    JOIN vol v ON r.numero_vol = v.numero_vol
    WHERE r.statut = 'En Attente'
    ORDER BY r.date_reservation DESC
");
$stmt->execute();
$pending_reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get confirmed reservations count
$stmt = $conn->prepare("SELECT COUNT(*) AS confirmed_count FROM reservation WHERE statut = 'Confirmée'");
$stmt->execute();
$confirmed_count = $stmt->get_result()->fetch_assoc()['confirmed_count'];

// Get confirmed reservations list
$stmt = $conn->prepare("
    SELECT r.*, u.nom as client_nom, u.prenom as client_prenom, v.numero_vol, v.destination
    FROM reservation r
    JOIN client c ON r.client_id = c.id
    JOIN utilisateur u ON c.id = u.id
    JOIN vol v ON r.numero_vol = v.numero_vol
    WHERE r.statut = 'Confirmée'
    ORDER BY r.date_reservation DESC
");
$stmt->execute();
$confirmed_reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get recent notifications
$stmt = $conn->prepare("
    SELECT * FROM notification 
    WHERE utilisateur_id = ? 
    ORDER BY date_envoi DESC 
    LIMIT 5
");
$stmt->bind_param('i', $gestionnaire_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get unread notifications count
$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM notification WHERE utilisateur_id = ? AND est_lue = 0");
$stmt->bind_param('i', $gestionnaire_id);
$stmt->execute();
$unread_count = $stmt->get_result()->fetch_assoc()['unread_count'];

// Get total reviews count
$stmt = $conn->prepare("SELECT COUNT(*) AS avis_count FROM avis");
$stmt->execute();
$avis_count = $stmt->get_result()->fetch_assoc()['avis_count'];

// Get reviews list
$stmt = $conn->prepare("
    SELECT a.*, u.nom as client_nom, u.prenom as client_prenom
    FROM avis a
    JOIN client c ON a.client_id = c.id
    JOIN utilisateur u ON c.id = u.id
    ORDER BY a.date_avis DESC
");
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gestionnaire - Agence de Voyage</title>
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
                    <span class="ml-4 text-xl font-semibold text-gray-800">Dashboard Gestionnaire</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="notificationBell" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center <?= $unread_count > 0 ? '' : 'hidden' ?>"><?= $unread_count ?></span>
                        </button>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50">
                            <div class="p-4 border-b">
                                <h3 class="text-lg font-semibold">Notifications</h3>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be loaded here -->
                            </div>
                        </div>
                    </div>
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
                <?= htmlspecialchars($gestionnaire['poste']) ?> - 
                <?= htmlspecialchars($gestionnaire['departement']) ?>
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-plane text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Réservations en attente</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= count($pending_reservations) ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Réservations confirmées</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $confirmed_count ?></p>
                    </div>
                </div>
                <button id="showConfirmedBtn" class="mt-4 w-full text-center px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                    Voir les réservations confirmées
                </button>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Avis</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $avis_count ?></p>
                    </div>
                </div>
                <button id="showReviewsBtn" class="mt-4 w-full text-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-opacity-75">
                    Voir les avis
                </button>
            </div>
        </div>

        <!-- Confirmed Reservations List -->
        <div id="confirmedReservationsSection" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Réservations confirmées</h2>
            <div id="confirmedReservationsList">
                <!-- Confirmed reservations will be loaded here by JS -->
            </div>
        </div>

        <!-- Pending Reservations -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Réservations en attente</h2>
            <?php if (empty($pending_reservations)): ?>
                <p class="text-gray-500 text-center py-4">Aucune réservation en attente</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pending_reservations as $reservation): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($reservation['client_prenom'] . ' ' . $reservation['client_nom']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($reservation['numero_vol']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($reservation['destination']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        DZD <?= number_format($reservation['montant_total'], 2, ',', ' ') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="viewReservation(<?= $reservation['id'] ?>)" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="confirmReservation(<?= $reservation['id'] ?>)"
                                                class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rejectReservation(<?= $reservation['id'] ?>)"
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Reviews List -->
        <div id="reviewsSection" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Avis des clients</h2>
            <div id="reviewsList">
                <!-- Reviews will be loaded here by JS -->
            </div>
        </div>
    </main>

    <!-- Reservation Modal -->
    <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-4xl p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Détails de la réservation</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="reservationDetails" class="mt-4">
                    <!-- Reservation details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Notification System
        let notificationDropdown = document.getElementById('notificationDropdown');
        let notificationBell = document.getElementById('notificationBell');
        let notificationCount = document.getElementById('notificationCount');
        let notificationList = document.getElementById('notificationList');

        // Toggle notification dropdown
        notificationBell.addEventListener('click', () => {
            notificationDropdown.classList.toggle('hidden');
            if (!notificationDropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Load notifications
        async function loadNotifications() {
            try {
                const response = await fetch('get_notifications.php');
                const data = await response.json();
                
                if (data.success) {
                    displayNotifications(data.notifications);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        // Display notifications
        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        Aucune notification
                    </div>
                `;
                notificationCount.classList.add('hidden');
                return;
            }

            const unreadNotifications = notifications.filter(notif => notif.est_lue === 0);
            notificationCount.textContent = unreadNotifications.length;
            notificationCount.classList.toggle('hidden', unreadNotifications.length === 0);

            notificationList.innerHTML = notifications.map(notification => `
                <div class="p-4 border-b hover:bg-gray-50 transition-colors ${notification.est_lue ? 'opacity-60' : ''}">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-800">${notification.message}</p>
                        <span class="text-xs text-gray-500">${notification.date_envoi}</span>
                    </div>
                </div>
            `).join('');
        }

        // Check for new notifications every minute
        setInterval(loadNotifications, 60000);

        // Reservation Management
        async function viewReservation(id) {
            try {
                const response = await fetch(`get_reservation_details.php?id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    const reservation = data.reservation;
                    const modal = document.getElementById('reservationModal');
                    const details = document.getElementById('reservationDetails');
                    
                    details.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-700">Informations Client</h4>
                                <p><span class="font-medium">Nom:</span> ${reservation.client_prenom} ${reservation.client_nom}</p>
                                <p><span class="font-medium">Email:</span> ${reservation.client_email}</p>
                                <p><span class="font-medium">Téléphone:</span> ${reservation.client_telephone}</p>
                            </div>
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-700">Informations Vol</h4>
                                <p><span class="font-medium">Numéro:</span> ${reservation.numero_vol}</p>
                                <p><span class="font-medium">Compagnie:</span> ${reservation.compagnie_aerienne}</p>
                                <p><span class="font-medium">Départ:</span> ${reservation.aeroport_depart}</p>
                                <p><span class="font-medium">Arrivée:</span> ${reservation.aeroport_arrivee}</p>
                                <p><span class="font-medium">Date départ:</span> ${reservation.date_depart}</p>
                                <p><span class="font-medium">Date arrivée:</span> ${reservation.date_arrivee}</p>
                            </div>
                            ${reservation.nom_hotel ? `
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-700">Informations Hôtel</h4>
                                    <p><span class="font-medium">Nom:</span> ${reservation.hotel_nom}</p>
                                    <p><span class="font-medium">Étoiles:</span> ${'⭐'.repeat(reservation.hotel_etoiles)}</p>
                                    <p><span class="font-medium">Ville:</span> ${reservation.hotel_ville}</p>
                                    <p><span class="font-medium">Pays:</span> ${reservation.hotel_pays}</p>
                                    <p><span class="font-medium">Type chambre:</span> ${reservation.type_chambre}</p>
                                    <p><span class="font-medium">Nombre chambres:</span> ${reservation.nombre_chambres}</p>
                                    <p><span class="font-medium">Date début:</span> ${reservation.date_debut ? reservation.date_debut : 'N/A'}</p>
                                    <p><span class="font-medium">Date fin:</span> ${reservation.date_fin ? reservation.date_fin : 'N/A'}</p>
                                </div>
                            ` : ''}
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-700">Informations Réservation</h4>
                                <p><span class="font-medium">Date réservation:</span> ${reservation.date_reservation}</p>
                                <p><span class="font-medium">Statut:</span> ${reservation.statut}</p>
                                <p><span class="font-medium">Montant total:</span> DZD ${reservation.montant_total}</p>
                                <p><span class="font-medium">Paiement:</span> ${reservation.est_paye}</p>
                            </div>
                        </div>
                    `;
                    
                    modal.classList.remove('hidden');
                } else {
                    alert('Erreur lors du chargement des détails de la réservation');
                }
            } catch (error) {
                console.error('Error loading reservation details:', error);
                alert('Erreur lors du chargement des détails de la réservation');
            }
        }

        async function confirmReservation(id) {
            if (confirm('Êtes-vous sûr de vouloir confirmer cette réservation ?')) {
                try {
                    const response = await fetch('update_reservation_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}&status=Confirmée`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('Réservation confirmée avec succès');
                        location.reload(); // Refresh to update the list
                    } else {
                        alert('Erreur lors de la confirmation: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error confirming reservation:', error);
                    alert('Erreur lors de la confirmation de la réservation');
                }
            }
        }

        async function rejectReservation(id) {
            if (confirm('Êtes-vous sûr de vouloir rejeter cette réservation ?')) {
                try {
                    const response = await fetch('update_reservation_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}&status=Rejetée`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('Réservation rejetée avec succès');
                        location.reload(); // Refresh to update the list
                    } else {
                        alert('Erreur lors du rejet: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error rejecting reservation:', error);
                    alert('Erreur lors du rejet de la réservation');
                }
            }
        }

        function closeModal() {
            document.getElementById('reservationModal').classList.add('hidden');
        }

        // Initial load of notifications
        loadNotifications();

        // Confirmed Reservations List
        const showConfirmedBtn = document.getElementById('showConfirmedBtn');
        const confirmedReservationsSection = document.getElementById('confirmedReservationsSection');
        const confirmedReservationsList = document.getElementById('confirmedReservationsList');

        showConfirmedBtn.addEventListener('click', () => {
            confirmedReservationsSection.classList.toggle('hidden');
            if (!confirmedReservationsSection.classList.contains('hidden')) {
                displayConfirmedReservations();
            }
        });

        function displayConfirmedReservations() {
            const confirmedReservations = <?= json_encode($confirmed_reservations) ?>;

            if (confirmedReservations.length === 0) {
                confirmedReservationsList.innerHTML = `
                    <p class="text-gray-500 text-center py-4">Aucune réservation confirmée</p>
                `;
                return;
            }

            let tableHtml = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;

            confirmedReservations.forEach(reservation => {
                tableHtml += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${reservation.client_prenom} ${reservation.client_nom}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${reservation.numero_vol}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${reservation.destination}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${new Date(reservation.date_reservation).toLocaleDateString('fr-FR')}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            DZD ${parseFloat(reservation.montant_total).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="viewReservation(${reservation.id})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            confirmedReservationsList.innerHTML = tableHtml;
        }

        // Reviews List
        const showReviewsBtn = document.getElementById('showReviewsBtn');
        const reviewsSection = document.getElementById('reviewsSection');
        const reviewsList = document.getElementById('reviewsList');
        const reviews = <?= json_encode($reviews) ?>;

        showReviewsBtn.addEventListener('click', () => {
            reviewsSection.classList.toggle('hidden');
            if (!reviewsSection.classList.contains('hidden')) {
                displayReviews();
            }
        });

        function displayReviews() {
            if (reviews.length === 0) {
                reviewsList.innerHTML = `
                    <p class="text-gray-500 text-center py-4">Aucun avis</p>
                `;
                return;
            }

            let tableHtml = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étoiles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;

            reviews.forEach(review => {
                tableHtml += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${review.client_prenom} ${review.client_nom}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${review.commentaire}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap">
                            ${'⭐'.repeat(review.stars)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${new Date(review.date_avis).toLocaleDateString('fr-FR')}
                        </td>
                    </tr>
                `;
            });

            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            reviewsList.innerHTML = tableHtml;
        }
    </script>
</body>
</html> 