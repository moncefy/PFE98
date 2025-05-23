<?php
session_start();

// Include the database connection if needed later
// require_once '../class/Database.php';

// You can fetch news articles from a database here if you have one
// For now, we will use placeholder content.

$newsArticles = [
    [
        'title' => 'Découvrez Nos Nouvelles Destinations Exotiques !',
        'image' => 'https://images.pexels.com/photos/3601425/pexels-photo-3601425.jpeg',
        'excerpt' => 'Explorez des îles tropicales, des montagnes majestueuses et des villes historiques lors de nos nouveaux voyages.',
        'link' => '#' // Link to a full article page if available
    ],
    [
        'title' => 'Offre Spéciale : 50% de Réduction sur les Vols pour l\'Europe',
        'image' => 'https://images.pexels.com/photos/164336/pexels-photo-164336.jpeg',
        'excerpt' => 'Ne manquez pas cette opportunité de voyager en Europe à moitié prix ! Réservez dès maintenant.',
        'link' => '#'
    ],
    [
        'title' => 'Conseils de Voyage : Comment Préparer Votre Aventure',
        'image' => 'https://images.pexels.com/photos/3155666/pexels-photo-3155666.jpeg',
        'excerpt' => 'Nos experts partagent leurs meilleurs conseils pour vous aider à planifier un voyage inoubliable.',
        'link' => '#'
    ],
      [
        'title' => 'Nouvelles Mesures de Sécurité dans les Aéroports',
        'image' => 'https://images.pexels.com/photos/7319307/pexels-photo-7319307.jpeg',
        'excerpt' => 'Restez informé des dernières mises à jour concernant les procédures de sécurité pour des voyages sereins.',
        'link' => '#'
    ],
      [
        'title' => 'Les Tendances de Voyage pour 2024',
        'image' => 'https://images.pexels.com/photos/3155666/pexels-photo-3155666.jpeg',
        'excerpt' => 'Découvrez les destinations et les types de voyages les plus populaires pour l\'année à venir.',
        'link' => '#'
    ],
       [
        'title' => 'Notre Engagement pour un Tourisme Durable',
        'image' => 'https://images.pexels.com/photos/3601425/pexels-photo-3601425.jpeg',
        'excerpt' => 'Apprenez-en davantage sur nos initiatives pour minimiser notre impact environnemental et soutenir les communautés locales.',
        'link' => '#'
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News - PFE Travels</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
  <!-- Navigation Bar (Similar to destinations.php) -->
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
              <a href="Services.php" class="text-gray-800 font-semibold hover:text-teal transition-colors">Services</a>
              <a href="News.php" class="text-teal font-semibold">News</a>
              <a href="welcome.php#footer" class="text-gray-800 font-semibold hover:text-teal transition-colors">Contact</a>
              
              <!-- Notification Bell (Placeholder for now) -->
              <div class="relative">
                  <button id="notificationBell" class="text-gray-800 hover:text-teal focus:outline-none">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                      </svg>
                      <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                  </button>
                  <!-- Notification Dropdown (Placeholder) -->
                  <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden z-50">
                       <div class="p-4 text-center text-gray-500">
                          Aucune notification
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

  <main class="mt-20 max-w-7xl mx-auto p-4">
      <h1 class="text-4xl font-extrabold text-gray-800 mb-8 text-center">Latest News & Offers</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($newsArticles as $article): ?>
              <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                  <img src="<?= $article['image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-48 object-cover">
                  <div class="p-6">
                      <h2 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($article['title']) ?></h2>
                      <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($article['excerpt']) ?></p>
                      <a href="<?= $article['link'] ?>" class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-300">
                          Read More <i class="fas fa-arrow-right text-xs ml-1"></i>
                      </a>
                  </div>
              </div>
          <?php endforeach; ?>
      </div>
  </main>

  <!-- Footer (Similar to destinations.php) -->
  <footer id="footer" class="bg-gray-200 text-gray-700 text-center p-4 mt-12">
      <div class="container mx-auto">
          <p>&copy; 2025 PFE Travels. All rights reserved.</p>
      </div>
  </footer>

    <script>
        // Basic script for profile dropdown (similar to destinations.php)
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
     <?php include '../includes/profile_modal.php'; ?>
</body>
</html> 