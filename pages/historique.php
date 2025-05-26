<?php
session_start();

// Temporarily enable error reporting for debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: Login.php');
    exit();
}

require_once '../class/Database.php';

// Initialize database connection
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

$client_id = $_SESSION['user_id'];

// Fetch client's reservation history, including flight and hotel info, and check for existing reviews
$stmt = $conn->prepare("
    SELECT 
        r.*,
        v.numero_vol as vol_numero_vol,
        v.compagnie_aerienne as vol_compagnie_aerienne,
        v.aeroport_depart as vol_aeroport_depart,
        v.aeroport_arrivee as vol_aeroport_arrivee,
        v.date_depart as vol_date_depart,
        v.date_arrivee as vol_date_arrivee,
        h.nom as hotel_nom,
        h.etoiles as hotel_etoiles,
        a.id as avis_id,
        a.stars as avis_stars,
        a.commentaire as avis_commentaire,
        a.date_avis as avis_date
    FROM reservation r
    JOIN vol v ON r.numero_vol = v.numero_vol
    LEFT JOIN hotel h ON r.nom_hotel = h.nom
    LEFT JOIN avis a ON r.id = a.reservation_id
    WHERE r.client_id = ?
    ORDER BY 
        CASE r.statut 
            WHEN 'Confirmée' THEN 1 
            WHEN 'En Attente' THEN 2 
            WHEN 'Rejetée' THEN 3 
            ELSE 4 
        END,
        r.date_reservation DESC
");
$stmt->bind_param('i', $client_id);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Historique de Réservations - Agence de Voyage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation (Include your existing navigation bar HTML here) -->
    <!-- You can copy the navigation from welcome.php and adjust paths -->
    <nav class="fixed top-0 left-0 right-0 bg-transparent backdrop-blur-md z-50 px-6 md:px-12 py-2">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="welcome.php">
                    <img src="../images/LOGO.png" alt="Logo" class="h-16 md:h-20 -my-4">
                </a>
            </div>
            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="welcome.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">Home</a>
                <a href="Services.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">Services</a>
                <a href="News.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">News</a>
                <a href="welcome.php#footer" class="text-gray-800 font-semibold hover:text-teal transition-colors">Contact</a>
                
                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notificationBell" class="text-gray-800 hover:text-teal focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden z-50">
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                        </div>
                        <div id="notificationList" class="max-h-60 overflow-y-auto">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center text-gray-800 hover:text-teal transition-colors focus:outline-none">
                            <i class="fas fa-circle-user text-xl mr-2"></i>
                            <?= htmlspecialchars($_SESSION['prenom']) ?>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-50">
                            <a href="#" id="openProfileModalBtn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="Historique.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Historique</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="Login.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">Join us</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon Historique</h1>

        <?php if (empty($reservations)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                Vous n'avez pas encore effectué de réservations.
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Réservation #<?= $reservation['id'] ?></h2>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $reservation['statut'] === 'Confirmée' ? 'bg-green-100 text-green-800' : ($reservation['statut'] === 'Rejetée' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <!-- Flight Info -->
                            <div class="space-y-2">
                                <h3 class="font-semibold text-gray-700">Informations Vol</h3>
                                <p><span class="font-medium">Numéro:</span> <?= htmlspecialchars($reservation['vol_numero_vol']) ?></p>
                                <p><span class="font-medium">Compagnie:</span> <?= htmlspecialchars($reservation['vol_compagnie_aerienne']) ?></p>
                                <p><span class="font-medium">Départ:</span> <?= htmlspecialchars($reservation['vol_aeroport_depart']) ?></p>
                                <p><span class="font-medium">Arrivée:</span> <?= htmlspecialchars($reservation['vol_aeroport_arrivee']) ?></p>
                                <p><span class="font-medium">Date départ:</span> <?= date('d/m/Y H:i', strtotime($reservation['vol_date_depart'])) ?></p>
                                <p><span class="font-medium">Date arrivée:</span> <?= date('d/m/Y H:i', strtotime($reservation['vol_date_arrivee'])) ?></p>
                            </div>
                            
                            <!-- Hotel Info (if applicable) -->
                            <?php if ($reservation['hotel_nom']): ?>
                                <div class="space-y-2">
                                    <h3 class="font-semibold text-gray-700">Informations Hôtel</h3>
                                    <p><span class="font-medium">Nom:</span> <?= htmlspecialchars($reservation['hotel_nom']) ?></p>
                                    <p><span class="font-medium">Étoiles:</span> <?= str_repeat('⭐', $reservation['hotel_etoiles']) ?></p>
                                    <p><span class="font-medium">Type chambre:</span> <?= htmlspecialchars($reservation['type_chambre']) ?></p>
                                    <p><span class="font-medium">Nombre chambres:</span> <?= htmlspecialchars($reservation['nombre_chambres']) ?></p>
                                    <p><span class="font-medium">Date début:</span> <?= !empty($reservation['date_debut_hotel']) ? date('d/m/Y', strtotime($reservation['date_debut_hotel'])) : 'N/A' ?></p>
                                    <p><span class="font-medium">Date fin:</span> <?= !empty($reservation['date_fin_hotel']) ? date('d/m/Y', strtotime($reservation['date_fin_hotel'])) : 'N/A' ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <p class="text-lg font-semibold text-gray-800">Montant Total: DZD <?= number_format($reservation['montant_total'], 2, ',', ' ') ?></p>
                                <p class="text-lg font-semibold <?= $reservation['est_paye'] == 1 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $reservation['est_paye'] == 1 ? 'Payé' : 'Non Payé' ?>
                                </p>
                            </div>
                            <p class="text-sm text-gray-600">Réservé le: <?= date('d/m/Y H:i', strtotime($reservation['date_reservation'])) ?></p>
                        </div>

                        <!-- Review Section -->
                        <div class="mt-4 pt-4 border-t">
                            <h3 class="font-semibold text-gray-700 mb-2">Avis</h3>
                            <?php if ($reservation['avis_id']): ?>
                                <!-- Existing Review -->
                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center space-x-1 mb-2 text-yellow-500">
                                                <?= str_repeat('<i class="fas fa-star"></i>', $reservation['avis_stars']) ?><?= str_repeat('<i class="far fa-star"></i>', 5 - $reservation['avis_stars']) ?>
                                            </div>
                                            <p class="text-gray-700 italic">"<?= htmlspecialchars($reservation['avis_commentaire']) ?>"</p>
                                            <p class="text-sm text-gray-500 mt-2">Posté le: <?= date('d/m/Y', strtotime($reservation['avis_date'])) ?></p>
                                        </div>
                                        <button onclick="deleteReview(<?= $reservation['avis_id'] ?>)" class="text-red-500 hover:text-red-700 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Leave a Review Button (Only for confirmed reservations) -->
                                <?php if ($reservation['statut'] === 'Confirmée'): ?>
                                    <div class="flex gap-2">
                                        <button class="leave-review-btn px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75" data-reservation-id="<?= $reservation['id'] ?>">
                                            Laisser un avis
                                        </button>
                                        <?php if ($reservation['est_paye'] == 0): ?>
                                            <button onclick="window.open('Payment_api.php', '_blank')"
                                                class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                                                Compléter le paiement
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                     <p class="text-gray-500 text-sm">Vous pourrez laisser un avis une fois votre réservation confirmée, votre voyage effectué et les services utilisés. Cela nous permettra de mieux évaluer votre expérience complète.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Laisser un avis</h3>
            <form id="reviewForm">
                <input type="hidden" id="modalReservationId" name="reservation_id">
                <input type="hidden" id="stars" name="stars" value="0">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Note (étoiles):</label>
                    <div class="flex items-center space-x-1" id="starRating">
                        <i class="far fa-star text-2xl text-yellow-400 cursor-pointer hover:text-yellow-500 transition-colors" data-rating="1"></i>
                        <i class="far fa-star text-2xl text-yellow-400 cursor-pointer hover:text-yellow-500 transition-colors" data-rating="2"></i>
                        <i class="far fa-star text-2xl text-yellow-400 cursor-pointer hover:text-yellow-500 transition-colors" data-rating="3"></i>
                        <i class="far fa-star text-2xl text-yellow-400 cursor-pointer hover:text-yellow-500 transition-colors" data-rating="4"></i>
                        <i class="far fa-star text-2xl text-yellow-400 cursor-pointer hover:text-yellow-500 transition-colors" data-rating="5"></i>
                    </div>
                    <p id="ratingText" class="text-sm text-gray-500 mt-1">Sélectionnez une note</p>
                </div>
                <div class="mb-4">
                    <label for="commentaire" class="block text-gray-700 font-medium mb-2">Votre commentaire:</label>
                    <textarea id="commentaire" name="commentaire" rows="4" class="form-textarea w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Écrivez votre avis ici..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelReviewBtn" class="mr-2 px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow hover:bg-gray-400">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-blue-600">Soumettre</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Modal -->
    <?php include '../includes/profile_modal.php'; ?>

    <script>
        console.log('Historique.php script started.');

        // Profile Dropdown
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileDropdownBtn && profileDropdown) {
            profileDropdownBtn.addEventListener('click', () => {
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        // Profile Modal Handling
        // This section will be handled by includes/profile_modal.php
        // const profileModal = document.getElementById('profileModal');
        // const openProfileModalBtn = document.getElementById('openProfileModalBtn');
        // const cancelProfileBtn = document.getElementById('cancelProfileBtn');
        // const profileForm = document.getElementById('profileForm');

        // Open modal
        // if (openProfileModalBtn) {
        //     openProfileModalBtn.addEventListener('click', (e) => {
        //         e.preventDefault(); // Prevent default link behavior
        //         profileDropdown.classList.add('hidden'); // Hide dropdown
        //         fetchClientDetails(); // Fetch and populate data
        //         profileModal.classList.remove('hidden');
        //     });
        // }

        // Close modal
        // if (cancelProfileBtn) {
        //     cancelProfileBtn.addEventListener('click', () => {
        //         profileModal.classList.add('hidden');
        //     });
        // }

        // Close modal when clicking outside
        // if (profileModal) {
        //     profileModal.addEventListener('click', (e) => {
        //         if (e.target === profileModal) {
        //             profileModal.classList.add('hidden');
        //         }
        //     });
        // }

        // Function to fetch client details (will be implemented in the next step)
        // async function fetchClientDetails() {
        //     console.log('Fetching client details...');
        //     try {
        //         const response = await fetch('get_client_details.php');
        //         const result = await response.json();
        //         console.log('Fetch client details response:', result);

        //         if (result.success && result.data) {
        //             document.getElementById('profile_prenom').value = result.data.prenom;
        //             document.getElementById('profile_nom').value = result.data.nom;
        //             document.getElementById('profile_email').value = result.data.email;
        //             document.getElementById('profile_telephone').value = result.data.telephone || ''; // Use empty string if telephone is null
        //         } else {
        //             console.error('Error fetching client details:', result.message);
        //             alert('Erreur lors du chargement des informations client.');
        //             profileModal.classList.add('hidden'); // Hide modal on error
        //         }
        //     } catch (error) {
        //         console.error('Error fetching client details:', error);
        //         alert('Une erreur est survenue lors du chargement des informations client.');
        //         profileModal.classList.add('hidden'); // Hide modal on error
        //     }
        // }

        // Handle Profile Form Submission (will be implemented in the next step)
        // if (profileForm) {
        //     profileForm.addEventListener('submit', async (e) => {
        //         e.preventDefault();
        //         console.log('Submitting profile form...');

        //         const formData = new FormData(profileForm);
        //         const clientData = Object.fromEntries(formData.entries());
        //         console.log('Form data to send:', clientData);

        //         try {
        //             const response = await fetch('update_client_profile.php', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/json'
        //                 },
        //                 body: JSON.stringify(clientData)
        //             });

        //             const result = await response.json();
        //             console.log('Update profile response:', result);

        //             if (result.success) {
        //                 alert('Profil mis à jour avec succès!');
        //                 profileModal.classList.add('hidden');
        //                 // Optionally, reload the page or update the header name if needed
        //                 location.reload(); 
        //             } else {
        //                 console.error('Error updating profile:', result.message);
        //                 alert('Erreur lors de la mise à jour du profil: ' + (result.message || 'Erreur inconnue'));
        //             }
        //         } catch (error) {
        //             console.error('Error submitting profile update:', error);
        //             alert('Une erreur est survenue lors de la mise à jour du profil. Veuillez réessayer.');
        //         }
        //     });
        // }

        // Review Modal Handling
        const reviewModal = document.getElementById('reviewModal');
        const reviewForm = document.getElementById('reviewForm');
        const modalReservationId = document.getElementById('modalReservationId');
        const cancelReviewBtn = document.getElementById('cancelReviewBtn');
        const leaveReviewBtns = document.querySelectorAll('.leave-review-btn');

        leaveReviewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const reservationId = btn.dataset.reservationId;
                modalReservationId.value = reservationId;
                reviewModal.classList.remove('hidden');
            });
        });

        // Star Rating System
        const starRating = document.getElementById('starRating');
        const stars = starRating.querySelectorAll('.fa-star');
        const ratingText = document.getElementById('ratingText');
        const starsInput = document.getElementById('stars');

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });

            // Update rating text
            const ratingTexts = {
                0: 'Sélectionnez une note',
                1: 'Très mauvais',
                2: 'Mauvais',
                3: 'Moyen',
                4: 'Bon',
                5: 'Excellent'
            };
            ratingText.textContent = ratingTexts[rating];
            starsInput.value = rating;
        }

        stars.forEach(star => {
            // Hover effect
            star.addEventListener('mouseover', () => {
                const rating = parseInt(star.dataset.rating);
                updateStars(rating);
            });

            // Click to select
            star.addEventListener('click', () => {
                const rating = parseInt(star.dataset.rating);
                updateStars(rating);
            });
        });

        // Reset stars when mouse leaves the container
        starRating.addEventListener('mouseleave', () => {
            const currentRating = parseInt(starsInput.value);
            updateStars(currentRating);
        });

        // Handle Review Form Submission
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(reviewForm);
            const reviewData = Object.fromEntries(formData.entries());

            // Validate rating
            if (parseInt(reviewData.stars) === 0) {
                alert('Veuillez sélectionner une note');
                return;
            }

            try {
                const formData = {
                    reservation_id: reviewData.reservation_id,
                    rating: reviewData.stars,
                    comment: reviewData.commentaire
                };
                console.log('Sending data:', formData); // Log the data being sent

                const response = await fetch('submit_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                console.log('Server response:', result); // Log the server response

                if (result.success) {
                    alert('Avis soumis avec succès!');
                    reviewModal.classList.add('hidden');
                    reviewForm.reset();
                    updateStars(0);
                    location.reload();
                } else {
                    console.error('Error details:', result); // Log error details
                    alert('Erreur lors de la soumission de l\'avis: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                alert('Une erreur est survenue lors de la soumission de l\'avis. Veuillez réessayer.');
            }
        });

        // Reset stars when modal is closed
        cancelReviewBtn.addEventListener('click', () => {
            reviewModal.classList.add('hidden');
            reviewForm.reset();
            updateStars(0);
        });

        // Function to delete a review
        async function deleteReview(reviewId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
                return;
            }

            try {
                const response = await fetch('delete_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        review_id: reviewId
                    })
                });

                const result = await response.json();
                console.log('Delete response:', result);

                if (result.success) {
                    alert('Avis supprimé avec succès!');
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression de l\'avis: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Error deleting review:', error);
                alert('Une erreur est survenue lors de la suppression de l\'avis. Veuillez réessayer.');
            }
        }
    </script>
</body>
</html> 