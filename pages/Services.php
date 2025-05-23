<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - PFE Travels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* Custom gradient background */
        .bg-gradient-custom {
            background: linear-gradient(to right top, #a78bfa, #818cf8, #6366f1, #4f46e5, #4338ca);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 bg-transparent backdrop-blur-md z-50 px-6 md:px-12 py-2">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="welcome.php">
                    <img src="../images/LOGO.png" alt="PFE Logo" class="h-16 md:h-20 -my-4">
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="welcome.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">Home</a>
                <a href="Services.php" class="text-teal font-semibold">Services</a>
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

    <!-- Hero Section -->
    <header class="bg-gradient-custom text-white py-24 text-center">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl md:text-5xl font-bold">Découvrez Nos Services Complets</h1>
            <p class="mt-4 text-lg opacity-90">Nous offrons une gamme de services pour faire de votre voyage une expérience inoubliable.</p>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Service Card 1 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-plane-departure text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Réservation de Vols</h3>
                    <p class="text-gray-600">Trouvez et réservez des vols aux meilleurs tarifs vers toutes vos destinations préférées.</p>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-hotel text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Réservation d'Hôtels</h3>
                    <p class="text-gray-600">Sélectionnez parmi une large gamme d'hôtels, des séjours économiques aux complexes de luxe.</p>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-car text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Location de Voitures</h3>
                    <p class="text-gray-600">Réservez une voiture de location pour une liberté totale pendant votre séjour.</p>
                </div>

                <!-- Service Card 4 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-route text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Forfaits Voyage</h3>
                    <p class="text-gray-600">Découvrez nos forfaits tout compris pour des voyages sans souci.</p>
                </div>

                <!-- Service Card 5 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-umbrella-beach text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Activités & Excursions</h3>
                    <p class="text-gray-600">Ajoutez des expériences locales incroyables à votre itinéraire.</p>
                </div>

                <!-- Service Card 6 -->
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-teal-600 mb-4">
                        <i class="fas fa-passport text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Assistance Visa & Passeport</h3>
                    <p class="text-gray-600">Nous vous aidons avec les démarches administratives pour vos documents de voyage.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Pourquoi Nous Choisir ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Image placeholder -->
                <div class="flex justify-center">
                    <img src="../images/why_choose_us.jpg" alt="Why Choose Us" class="rounded-lg shadow-xl max-h-96 object-cover">
                </div>

                <div class="space-y-6">
                    <!-- Feature 1 -->
                    <div class="flex items-start space-x-4">
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Tarifs Compétitifs</h3>
                            <p class="text-gray-600">Nous négocions les meilleurs prix pour vous offrir les offres les plus avantageuses.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-start space-x-4">
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Large Sélection</h3>
                            <p class="text-gray-600">Accédez à un vaste choix de destinations, vols, hôtels et activités.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start space-x-4">
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Support Client Dédié</h3>
                            <p class="text-gray-600">Notre équipe est disponible pour vous aider avant, pendant et après votre voyage.</p>
                        </div>
                    </div>

                     <!-- Feature 4 -->
                    <div class="flex items-start space-x-4">
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Processus Simple et Rapide</h3>
                            <p class="text-gray-600">Notre plateforme intuitive rend la planification et la réservation de votre voyage faciles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 bg-gradient-custom text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à Planifier Votre Prochain Voyage ?</h2>
            <p class="text-lg mb-8 opacity-90">Contactez-nous dès aujourd'hui ou commencez à rechercher votre destination idéale.</p>
            <a href="welcome.php#footer" class="bg-white text-purple-600 font-semibold py-3 px-8 rounded-full hover:bg-gray-200 transition-colors text-lg">
                Contactez-nous
            </a>
        </div>
    </section>

    <!-- Footer (Similar to other pages) -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                     <a href="welcome.php">
                         <img src="../images/LOGO.png" alt="PFE Logo" class="h-16 md:h-20 -my-4">
                     </a>
                    <p class="text-gray-300 mt-4">Your trusted travel partner since 2025. We make your dream vacations come true.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-300 hover:text-teal-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-300 hover:text-teal-400 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-teal-400 transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liens Rapides</h4>
                    <ul class="space-y-2">
                        <li><a href="welcome.php" class="text-gray-300 hover:text-teal-400 transition-colors">Accueil</a></li>
                         <li><a href="Services.php" class="text-gray-300 hover:text-teal-400 transition-colors">Services</a></li>
                        <li><a href="destinations.php" class="text-gray-300 hover:text-teal-400 transition-colors">Destinations</a></li>
                        <li><a href="News.php" class="text-gray-300 hover:text-teal-400 transition-colors">Actualités</a></li>
                        <li><a href="welcome.php#footer" class="text-gray-300 hover:text-teal-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Info Contact</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>Bloc 5, Faculté des Sciences, Boumerdes</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2"></i>
                            <span>+213 541 493 604</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>info@pfetravels.com</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Newsletter</h4>
                    <p class="text-gray-300 mb-4">Abonnez-vous à notre newsletter pour les dernières mises à jour et offres.</p>
                    <form class="flex">
                        <input type="email" placeholder="Votre email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-gray-900">
                        <button class="bg-teal-600 px-4 py-2 rounded-r-lg hover:bg-teal-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400">
                <p>&copy; 2025 PFE Travels. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <?php include '../includes/profile_modal.php'; ?>

     <script>
        // Basic script for profile dropdown
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

        // Placeholder for notification bell toggle
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBell && notificationDropdown) {
             notificationBell.addEventListener('click', () => {
                notificationDropdown.classList.toggle('hidden');
             });

            document.addEventListener('click', (e) => {
                if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html> 