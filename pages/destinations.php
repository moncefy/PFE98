<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

// destinations.php
require_once '../class/Database.php';

// Connexion à la base
$db   = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Query to get distinct continents - Re-added
$sql_continents = "SELECT DISTINCT continent FROM pays";
$stmt_continents = $conn->prepare($sql_continents);
$stmt_continents->execute();
$continents = $stmt_continents->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_continents->close();

// Récupération des filtres
$searchQuery = trim($_GET['search'] ?? '');
$continent   = $_GET['continent'] ?? '';
$maxPrice    = $_GET['max_price'] ?? '';

// Construction de la requête SQL
$sql    = "SELECT * FROM vol WHERE 1";
$params = [];
if ($searchQuery) {
    $sql      .= " AND destination LIKE ?";
    $params[] = "%$searchQuery%";
}
if ($continent) {
    $sql      .= " AND LOWER(continent) = LOWER(?)";
    $params[] = $continent;
}
if ($maxPrice) {
    $sql      .= " AND prix <= ?";
    $params[] = $maxPrice;
}

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Récupérer les hôtels
$hotels = [];
$hotelQuery = $conn->query("SELECT id, nom, prix_par_nuit, chambres_disponible, ville, pays, etoiles FROM hotel");
while ($row = $hotelQuery->fetch_assoc()) {
    $hotels[] = $row;
}
$conn->close();

// Images fictives par destination
$imageMap = [
  'Afghanistan' => 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Buddhas_of_Bamiyan_2001.jpg',
  'Afrique du Sud' => 'https://upload.wikimedia.org/wikipedia/commons/e/e1/Cape_Town_Panorama.jpg',
  'Algérie' => 'https://upload.wikimedia.org/wikipedia/commons/0/05/El_Djazaïr_Alger_vue_du_ciel.jpg',
  'Allemagne' => 'https://upload.wikimedia.org/wikipedia/commons/7/74/Brandenburger_Tor_abends.jpg',
  'Arabie saoudite' => 'https://upload.wikimedia.org/wikipedia/commons/a/a5/Al_Masjid_Al_Haram.jpg',
  'Argentine' => 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Iguazu-008.jpg',
  'Australie' => 'https://upload.wikimedia.org/wikipedia/commons/6/6e/Sydney_Opera_House_-_Dec_2008.jpg',
  'Belgique' => 'https://upload.wikimedia.org/wikipedia/commons/2/29/La_Grand-Place_%28Brussels%29.jpg',
  'Brésil' => 'https://upload.wikimedia.org/wikipedia/commons/6/6b/Rio_de_Janeiro_Corcovado.jpg',
  'Cambodge' => 'https://upload.wikimedia.org/wikipedia/commons/d/d9/Angkor_Wat.jpg',
  'Canada' => 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Toronto_Skyline.jpg',
  'Chili' => 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Moai_Rano_raraku.jpg',
  'Chine' => 'https://upload.wikimedia.org/wikipedia/commons/f/f4/GreatWallBadaling.jpg',
  'Corée du Sud' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/Gyeongbokgung-palace.jpg',
  'Cuba' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/La_Habana_Vieja_4.jpg',
  'Danemark' => 'https://upload.wikimedia.org/wikipedia/commons/0/0a/Little_mermaid_statue_Copenhagen_Denmark.jpg',
  'Égypte' => 'https://upload.wikimedia.org/wikipedia/commons/e/e3/Kheops-Pyramid.jpg',
  'Émirats arabes unis' => 'https://upload.wikimedia.org/wikipedia/commons/c/c9/Burj_Khalifa_2021.jpg',
  'Espagne' => 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Sagrada_Familia_01.jpg',
  'États-Unis' => 'https://images.unsplash.com/photo-1598344689264-30ed6f21055c?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
  'Finlande' => 'https://upload.wikimedia.org/wikipedia/commons/7/76/Helsinki_Cathedral_in_July_2004.jpg',
    'France' => 'https://upload.wikimedia.org/wikipedia/commons/e/e6/Paris_Night.jpg',
  'Grèce' => 'https://upload.wikimedia.org/wikipedia/commons/d/d8/Acropolis_of_Athens_Sunset.jpg',
  'Inde' => 'https://upload.wikimedia.org/wikipedia/commons/d/da/Taj-Mahal.jpg',
  'Indonésie' => 'https://upload.wikimedia.org/wikipedia/commons/6/6d/Borobudur-Nothwest-view.jpg',
  'Islande' => 'https://upload.wikimedia.org/wikipedia/commons/1/1e/Hallgrimskirkja_2009-08-20.jpg',
  'Israël' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Jerusalem_Walls_8249.jpg',
  'Italie' => 'https://upload.wikimedia.org/wikipedia/commons/4/4e/Colosseum_in_Rome%2C_Italy_-_April_2007.jpg',
  'Japon' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Mount_Fuji_and_Chureito_Pagoda.jpg',
  'Kenya' => 'https://upload.wikimedia.org/wikipedia/commons/3/3a/Mount_Kenya_view.jpg',
  'Maroc' => 'https://upload.wikimedia.org/wikipedia/commons/0/0a/Jemaa_el-Fnaa_%281%29.jpg',
  'Mexique' => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Mexico_City_Zocalo.jpg',
  'Norvège' => 'https://upload.wikimedia.org/wikipedia/commons/7/70/Preikestolen_Drone_2016.jpg',
  'Nouvelle-Zélande' => 'https://upload.wikimedia.org/wikipedia/commons/3/3d/Hobbiton_New_Zealand.jpg',
  'Pérou' => 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Machu_Picchu%2C_Peru.jpg',
  'Portugal' => 'https://upload.wikimedia.org/wikipedia/commons/6/63/Ponte25deAbril-Jan2007.jpg',
  'Royaume-Uni' => 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Buckingham_Palace_2015.jpg',
  'Russie' => 'https://upload.wikimedia.org/wikipedia/commons/f/f6/Saint_Basil%27s_Cathedral_and_Red_Square.jpg',
  'Suisse' => 'https://upload.wikimedia.org/wikipedia/commons/5/58/Matterhorn_from_Domhütte_-_2.jpg',
  'Thaïlande' => 'https://upload.wikimedia.org/wikipedia/commons/d/d7/Bangkok_Wat_Arun.jpg',
  'Turquie' => 'https://upload.wikimedia.org/wikipedia/commons/2/2b/Blue_Mosque_Courtyard_Dusk_Wikimedia_Commons.jpg',
  'Vietnam' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Halong_bay_DAF.jpg',
  'Côte d\'Ivoire' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Basilique_Notre-Dame_de_la_Paix.jpg',
  'Malaisie' => 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Petronas_Twin_Towers_Malaysia.jpg',
  'Autriche' => 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Wien_-_Stephansdom_%281%29.JPG',
  'Irlande' => 'https://upload.wikimedia.org/wikipedia/commons/4/45/Cliffs_of_Moher_%288011306702%29.jpg',
  'Colombie' => 'https://upload.wikimedia.org/wikipedia/commons/a/a3/Ciudad_Amurallada%2C_Cartagena.jpg',
  'Pologne' => 'https://upload.wikimedia.org/wikipedia/commons/3/3d/Warsaw_Old_Town_Market_Square_02.JPG'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Résultats des vols</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <!-- Navigation Bar -->
  <nav class="fixed top-0 left-0 right-0 bg-transparent backdrop-blur-md z-50 px-6 md:px-12 py-2">
      <div class="max-w-7xl mx-auto flex justify-between items-center">
          <div class="flex items-center">
              <img src="../images/LOGO.png" alt="PFE Logo" class="h-16 md:h-20 -my-4">
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex space-x-6 items-center">
        <a href="welcome.php" class="text-gray-600 hover:text-gray-800">Accueil</a>
              <a href="Discover.php" class="text-gray-600 hover:text-gray-800">Discover</a>
              <a href="#" class="text-gray-600 hover:text-gray-800">Services</a>
              <a href="#" class="text-gray-600 hover:text-gray-800">News</a>
              <a href="#" class="text-gray-600 hover:text-gray-800">Contact</a>
              
              <!-- Notification Bell -->
              <div class="relative">
                  <button id="notificationBell" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                      </svg>
                      <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                  </button>
                  
                  <!-- Notification Dropdown -->
                  <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden z-50">
                      <div class="p-4 border-b flex justify-between items-center">
                          <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                          <button id="markAllReadBtn" class="text-sm text-indigo-600 hover:text-indigo-800">Mark all as read</button>
                      </div>
                      <div id="notificationList" class="max-h-96 overflow-y-auto">
                          <!-- Notifications will be loaded here -->
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </nav>

  <main class="mt-20 max-w-7xl mx-auto p-4">
    <!-- Search Section -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow flex flex-col md:flex-row gap-4 items-center">
        <form action="destinations.php" method="GET" class="w-full flex flex-wrap gap-4 items-center" onsubmit="return validateForm()">
            <div class="flex-1 flex items-center gap-2 relative">
                <input type="text" 
                       id="search" 
                       name="search" 
                       class="border rounded p-2 flex-1"
                       placeholder="Où voulez-vous aller?"
                       value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                       autocomplete="off"
                       oninput="fetchSuggestions(this.value)">
                
                <!-- Suggestions box -->
                <div id="suggestions" class="absolute z-10 w-full top-full mt-2 bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <select name="continent" class="border rounded p-2 cursor-pointer">
        <option value="">Tous les continents</option>
                    <?php foreach ($continents as $cont): ?>
                        <option value="<?php echo htmlspecialchars($cont['continent']); ?>">
                            <?php echo htmlspecialchars($cont['continent']); ?>
                        </option>
                    <?php endforeach; ?>
      </select>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <input type="number" 
                       name="max_price" 
                       class="border rounded p-2 w-40"
                       placeholder="Prix maximum"
                       value="<?= htmlspecialchars($maxPrice ?? '') ?>">
            </div>

            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                Rechercher
            </button>
        </form>
    </div>

    <?php if (empty($results)): ?>
      <p class="text-center text-gray-500">Aucun vol trouvé.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($results as $vol): 
          $dest = ucfirst(strtolower($vol['destination']));
          // Detailed debugging
          echo "<!-- Debug: Destination='$dest', Airport='{$vol['aeroport_arrivee']}' -->";
          
          // Check if it's a US destination and which airport
          if ($dest === 'États-unis' || $dest === 'États-Unis') {
            echo "<!-- Debug: This is a US destination -->";
            if (stripos($vol['aeroport_arrivee'], 'New York JFK') !== false) {
              $img = '../images/NYC.png';
              $bgPosition = 'center 70%';
              echo "<!-- Debug: Using NYC image -->";
            } elseif (stripos($vol['aeroport_arrivee'], 'Los Angeles LAX') !== false) {
              $img = '../images/LA.png';
              $bgPosition = 'center center';
              echo "<!-- Debug: Using LA image -->";
            } else {
              $img = $imageMap[$dest] ?? 'https://via.placeholder.com/400x300?text=' . urlencode($dest);
              $bgPosition = 'center center';
              echo "<!-- Debug: Using default image -->";
            }
          } else {
            $img = $imageMap[$dest] ?? 'https://via.placeholder.com/400x300?text=' . urlencode($dest);
            $bgPosition = 'center center';
          }
          echo "<!-- Debug: Final image path='$img' -->";
        ?>
        <div class="vol-card bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col"
             data-dest="<?= htmlspecialchars($vol['destination']) ?>"
             data-continent="<?= htmlspecialchars(strtolower($vol['continent'])) ?>"
             data-price="<?= $vol['prix'] ?>"
             data-type_vol="<?= htmlspecialchars(strtolower($vol['type_vol'])) ?>"
             data-places="<?= $vol['places_disponibles'] ?>">
          <div class="h-40 bg-cover" style="background-image:url('<?= $img ?>'); background-position: <?= $bgPosition ?>"></div>
          <div class="p-4 flex-1 flex flex-col">
            <h3 class="text-indigo-600 font-bold text-xl mb-2">Vol : <?= $vol['numero_vol'] ?></h3>
            <p class="text-gray-700"><strong>Compagnie :</strong> <?= $vol['compagnie_aerienne'] ?></p>
            <p class="text-gray-700"><strong>Départ :</strong> <?= $vol['aeroport_depart'] ?></p>
            <p class="text-gray-700"><strong>Arrivée :</strong> <?= $vol['aeroport_arrivee'] ?></p>
            <p class="text-gray-700"><strong>Dest. :</strong> <?= $vol['destination'] ?></p>
            <p class="text-gray-700"><strong>Date :</strong> <?= date('d/m H:i', strtotime($vol['date_depart'])) ?></p>
            <p class="text-indigo-600 font-semibold mt-auto">DZD <?= number_format($vol['prix'],2,',',' ') ?></p>
            <button onclick='openModal(<?= json_encode($vol) ?>)'
              class="mt-4 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
              Réserver
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Notification Container -->
  <div id="notification" class="fixed top-4 right-4 z-50 transform transition-all duration-500 translate-x-full">
    <div class="bg-white rounded-lg shadow-lg p-4 flex items-center gap-3">
      <div id="notificationIcon" class="w-8 h-8 rounded-full flex items-center justify-center">
        <!-- Icon will be added by JavaScript -->
      </div>
      <div>
        <h3 id="notificationTitle" class="font-semibold text-gray-800"></h3>
        <p id="notificationMessage" class="text-gray-600"></p>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="min-h-screen px-4 text-center">
      <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
      <div class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
        <div class="flex items-center justify-center mb-4">
          <div id="confirmationIcon" class="w-12 h-12 rounded-full flex items-center justify-center">
            <!-- Icon will be added by JavaScript -->
          </div>
        </div>
        <h3 id="confirmationTitle" class="text-lg font-medium text-gray-900 text-center mb-2"></h3>
        <p id="confirmationMessage" class="text-sm text-gray-500 text-center"></p>
        <div class="mt-6 flex justify-center">
          <button id="generateInvoiceBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors mr-2">
            Generer Facture
          </button>
          <button id="confirmationClose" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="min-h-screen px-4 text-center">
      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
      <div class="inline-block w-11/12 md:w-2/3 lg:w-1/2 p-8 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div id="modalBody" class="space-y-4"></div>
      </div>
    </div>
  </div>

  <script>
    const hotels = <?= json_encode($hotels) ?>;
    let basePrice = 0;
    let hotelPrice = 0;
    let transportCost = 0;
    let nombreBillets = 1;

    function updateTotal() {
      const billetsInput = document.getElementById('nombreBillets');
      if (billetsInput) {
        nombreBillets = parseInt(billetsInput.value) || 1;
      }

      let currentHotelPricePerNight = 0;
      const hotelSel = document.getElementById('hotelSel');
      if (hotelSel && hotelSel.selectedOptions.length > 0 && hotelSel.selectedOptions[0].dataset.price) {
        currentHotelPricePerNight = parseFloat(hotelSel.selectedOptions[0].dataset.price);
      }

      let numRooms = parseInt(document.getElementById('numRoomsInput')?.value) || 0;

      let dateDebut = document.getElementById('dateDebutHotelInput')?.value;
      let dateFin = document.getElementById('dateFinInput')?.value;
      let numberOfNights = 0;

      if (dateDebut && dateFin) {
        const startDate = new Date(dateDebut);
        const endDate = new Date(dateFin);
        const timeDiff = endDate.getTime() - startDate.getTime();
        numberOfNights = timeDiff > 0 ? Math.ceil(timeDiff / (1000 * 3600 * 24)) : 0; // Calculate days, round up to count the last night
      }

      hotelPrice = currentHotelPricePerNight * numRooms * numberOfNights;

      console.log('updateTotal called:');
      console.log('  basePrice:', basePrice);
      console.log('  nombreBillets:', nombreBillets);
      console.log('  currentHotelPricePerNight:', currentHotelPricePerNight);
      console.log('  numRooms:', numRooms);
      console.log('  dateDebut:', dateDebut);
      console.log('  dateFin:', dateFin);
      console.log('  numberOfNights:', numberOfNights);
      console.log('  calculated hotelPrice:', hotelPrice);
      console.log('  transportCost:', transportCost);
      console.log('  total:', (basePrice * nombreBillets) + hotelPrice + transportCost);

      const total = (basePrice * nombreBillets) + hotelPrice + transportCost;
      const totalElement = document.getElementById('totalPrice');
      if (totalElement) {
        totalElement.textContent = `Total : DZD ${total.toLocaleString('fr-FR')}`;
      }
    }

    function openModal(flight) {
      document.getElementById('modal').classList.remove('hidden');
      basePrice = parseFloat(flight.prix);
      hotelPrice = 0;
      transportCost = 0;
      nombreBillets = 1;

      let filteredHotels = hotels.filter(h => h.pays?.toLowerCase() === flight.destination.toLowerCase());
      let hotelOptions = filteredHotels.length
        ? filteredHotels.map(h => `<option value="${h.id}" data-price="${h.prix_par_nuit}" data-type="${h.type_chambre}" data-dispo="${h.chambres_disponible}">${h.nom} (${h.etoiles} ⭐) - DZD ${h.prix_par_nuit}</option>`).join('')
        : `<option disabled>Aucun hôtel disponible à cette destination</option>`;

      const html = `
        <h2 class="text-2xl font-bold text-indigo-700 mb-6">Réservation Vol ${flight.numero_vol}</h2>
        
        <div class="bg-indigo-50 p-4 rounded-lg mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de billets :</label>
          <div class="flex items-center gap-4">
            <button type="button" onclick="document.getElementById('nombreBillets').value = Math.max(1, parseInt(document.getElementById('nombreBillets').value) - 1); updateTotal()" 
              class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-200 transition-colors">-</button>
            <input type="number" id="nombreBillets" min="1" max="${flight.places_disponibles}" value="1" 
              class="w-20 text-center border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              onchange="updateTotal()">
            <button type="button" onclick="document.getElementById('nombreBillets').value = Math.min(${flight.places_disponibles}, parseInt(document.getElementById('nombreBillets').value) + 1); updateTotal()" 
              class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-200 transition-colors">+</button>
            <span class="text-sm text-gray-500">(max: ${flight.places_disponibles} places disponibles)</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-600"><strong>Départ :</strong> ${flight.aeroport_depart}</p>
            <p class="text-gray-600"><strong>Arrivée :</strong> ${flight.aeroport_arrivee}</p>
            <p class="text-gray-600"><strong>Places disponibles :</strong> ${flight.places_disponibles}</p>
            <p class="text-gray-600"><strong>Type de vol :</strong> ${flight.type_vol}</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-600"><strong>Date départ :</strong> ${new Date(flight.date_depart).toLocaleString()}</p>
            <p class="text-gray-600"><strong>Prix vol :</strong> DZD ${parseFloat(flight.prix).toLocaleString('fr-FR')}</p>
          </div>
        </div>

        <div class="bg-indigo-50 p-4 rounded-lg mb-6">
          <label class="flex items-center gap-2 text-indigo-700 font-medium">
            <input type="checkbox" id="hotelCB" class="rounded text-indigo-600"> Ajouter hôtel
        </label>
          <div id="hotelSection" class="mt-4 hidden space-y-4">
            <select id="hotelSel" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choisir un hôtel --</option>
            ${hotelOptions}
          </select>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de chambres:</label>
                <div class="flex items-center gap-4">
                  <button type="button" onclick="document.getElementById('numRoomsInput').value = Math.max(1, parseInt(document.getElementById('numRoomsInput').value) - 1); updateTotal()"
                    class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-200 transition-colors">-</button>
                  <input type="number" id="numRoomsInput" min="1" value="1"
                    class="w-20 text-center border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    onchange="updateTotal()">
                  <button type="button" onclick="document.getElementById('numRoomsInput').value = parseInt(document.getElementById('numRoomsInput').value) + 1; updateTotal()"
                    class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-200 transition-colors">+</button>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de chambre:</label>
                <select id="roomTypeSel" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                  <option value="simple">Simple</option>
                  <option value="double">Double</option>
                  <option value="suite">Suite</option>
                  <option value="familiale">Familiale</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début (hôtel):</label>
                <input type="date" id="dateDebutHotelInput" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin (hôtel):</label>
                <input type="date" id="dateFinInput" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
            </div>
            <p id="hotelInfo" class="text-indigo-600 font-medium"></p>
          </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Transport :</label>
          <select id="transportSel" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="0">-- Aucun --</option>
            <option value="2500">Bus (+ DZD 2 500)</option>
            <option value="5000">Taxi (+ DZD 5 000)</option>
            <option value="3000">Train (+ DZD 3 000)</option>
        </select>
        </div>

        <div class="bg-green-50 p-4 rounded-lg mb-6">
          <p id="totalPrice" class="text-xl font-bold text-green-600">Total : DZD ${parseFloat(flight.prix).toLocaleString('fr-FR')}</p>
        </div>

        <button onclick='confirmBooking(${JSON.stringify(flight)})'
          class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium text-lg">
          Confirmer la réservation
        </button>
      `;

      document.getElementById('modalBody').innerHTML = html;

      document.getElementById('nombreBillets').addEventListener('change', updateTotal);
      document.getElementById('hotelCB').addEventListener('change', e => {
        document.getElementById('hotelSection').classList.toggle('hidden', !e.target.checked);
        if (!e.target.checked) {
          hotelPrice = 0;
          // Reset hotel related inputs when unchecking
          document.getElementById('hotelSel').value = "";
          document.getElementById('numRoomsInput').value = 1;
          document.getElementById('roomTypeSel').value = "simple";
          document.getElementById('dateDebutHotelInput').value = "";
          document.getElementById('dateFinInput').value = "";
          document.getElementById('hotelInfo').textContent = '';
          updateTotal();
        }
      });

      document.getElementById('hotelSel').addEventListener('change', e => {
        console.log('Hotel selection changed');
        const sel = e.target.selectedOptions[0];
        const dispo = sel.dataset.dispo; // Keep for display
        const type = sel.dataset.type;
        document.getElementById('hotelInfo').textContent = dispo && type ? `${dispo} chambres disponibles` : '';
        updateTotal();
      });

      document.getElementById('numRoomsInput').addEventListener('change', updateTotal);
      document.getElementById('roomTypeSel').addEventListener('change', updateTotal); // Although room type doesn't affect price, keep updateTotal call for consistency if needed later
      document.getElementById('dateDebutHotelInput').addEventListener('change', updateTotal);
      document.getElementById('dateFinInput').addEventListener('change', updateTotal);

      document.getElementById('transportSel').addEventListener('change', e => {
        transportCost = parseFloat(e.target.value);
        updateTotal();
      });

      // Initial total update
      updateTotal();
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
    }

    async function confirmBooking(flight) {
      const hotelCheckbox = document.getElementById('hotelCB');
      const hotelSelected = hotelCheckbox.checked;
      let hotelName = null;
      let nombreChambres = 0;
      let typeChambre = null;
      let dateDebutHotel = null;
      let dateFin = null;
      const nombreBillets = parseInt(document.getElementById('nombreBillets').value) || 1;

      if (hotelSelected) {
        const hotelSel = document.getElementById('hotelSel');
        const selectedOption = hotelSel.selectedOptions[0];
        if (selectedOption && selectedOption.value !== "") {
          hotelName = selectedOption.text.split(' (')[0].trim();
          nombreChambres = parseInt(document.getElementById('numRoomsInput').value, 10);
          typeChambre = document.getElementById('roomTypeSel').value;
          dateDebutHotel = document.getElementById('dateDebutHotelInput').value;
          dateFin = document.getElementById('dateFinInput').value;
        }
      }

      const transportCost = parseFloat(document.getElementById('transportSel').value);
      const totalAmount = parseFloat(document.getElementById('totalPrice').textContent.replace('Total : DZD', '').replace(/\s/g, '').replace(',', '.'));

      const reservationData = {
        numero_vol: flight.numero_vol,
        nom_hotel: hotelName,
        nombre_chambres: nombreChambres,
        type_chambre: typeChambre,
        date_debut_hotel: dateDebutHotel,
        date_fin_hotel: dateFin,
        nombre_billets: nombreBillets,
        montant_total: totalAmount,
      };

      try {
        const response = await fetch('save_reservation.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(reservationData)
        });

        const result = await response.json();

        if (result.success) {
          // Add notification after successful reservation
          try {
            console.log('Sending notification...');
            const notificationResponse = await fetch('save_notification.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                utilisateur_id: 1, // For testing, we'll use ID 1
                message: `Votre réservation pour le vol ${flight.numero_vol} a été envoyée et est en attente de traitement.`
              })
            });
            
            const notificationResult = await notificationResponse.json();
            console.log('Notification result:', notificationResult);
            
            if (!notificationResult.success) {
              console.error('Failed to save notification:', notificationResult.message);
            }
          } catch (notificationError) {
            console.error('Error saving notification:', notificationError);
          }

          showNotification('success', 'Réservation confirmée', 'Votre réservation a été enregistrée avec succès !');
          // Pass the reservation_id to the confirmation modal
          showConfirmationModal('success', 'Réservation confirmée', 'Votre réservation a été enregistrée avec succès !', result.reservation_id);
      closeModal();
        } else {
          showNotification('error', 'Erreur', result.message || 'Une erreur est survenue lors de la réservation.');
        }
      } catch (error) {
        console.error('Error during reservation:', error);
        showNotification('error', 'Erreur', 'Une erreur est survenue lors de la réservation.');
      }
    }

    // Add these new functions for notifications
    function showNotification(type, title, message) {
      const notification = document.getElementById('notification');
      const icon = document.getElementById('notificationIcon');
      const titleEl = document.getElementById('notificationTitle');
      const messageEl = document.getElementById('notificationMessage');

      // Reset previous styles
      notification.classList.remove('bg-white', 'shadow-lg');

      // Set background and shadow based on type
      if (type === 'success') {
        notification.classList.add('bg-green-50', 'border', 'border-green-200', 'shadow-md');
        icon.innerHTML = `
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        `;
        titleEl.textContent = 'Succès';
        titleEl.classList.remove('text-gray-800', 'text-red-800');
        titleEl.classList.add('text-green-800');

      } else if (type === 'error') {
         notification.classList.add('bg-red-50', 'border', 'border-red-200', 'shadow-md');
        icon.innerHTML = `
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        `;
        titleEl.textContent = 'Erreur';
         titleEl.classList.remove('text-gray-800', 'text-green-800');
        titleEl.classList.add('text-red-800');
      } else { // Default/Info
        notification.classList.add('bg-blue-50', 'border', 'border-blue-200', 'shadow-md');
         icon.innerHTML = `
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        `;
         titleEl.textContent = title; // Use provided title for info
          titleEl.classList.remove('text-gray-800', 'text-green-800', 'text-red-800');
         titleEl.classList.add('text-blue-800');
      }

      messageEl.textContent = message;
       messageEl.classList.remove('text-gray-600'); // Remove default text color
        titleEl.classList.add('font-semibold'); // Ensure title is bold

      // Show notification with subtle animation
      notification.classList.remove('translate-x-full');
      notification.classList.add('translate-x-0');
      
      // Hide after 5 seconds with animation
      setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
      }, 5000);
    }

    function showConfirmationModal(type, title, message, reservation_id) {
      const modal = document.getElementById('confirmationModal');
      const icon = document.getElementById('confirmationIcon');
      const titleEl = document.getElementById('confirmationTitle');
      const messageEl = document.getElementById('confirmationMessage');

      // Set icon based on type
      if (type === 'success') {
        icon.innerHTML = `
          <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        `;
      } else {
        icon.innerHTML = `
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        `;
      }

      titleEl.textContent = title;
      messageEl.textContent = message;

      // Show modal
      modal.classList.remove('hidden');

      // Update modal footer with both buttons
      const modalFooter = modal.querySelector('.mt-6.flex.justify-center');
      if (modalFooter) {
          modalFooter.innerHTML = `
              <button id=\"generateInvoiceBtn\" class=\"px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors mr-2\" data-reservation-id=\"${reservation_id}\">
                  Generer Facture
              </button>
              <button id=\"confirmationClose\" class=\"px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors\">
                  Fermer
              </button>
          `;
          
          // Add event listener for the new generate invoice button
          document.getElementById('generateInvoiceBtn').onclick = () => {
              const reservationId = document.getElementById('generateInvoiceBtn').getAttribute('data-reservation-id');
              if (reservationId) {
                  window.open(`generate_invoice.php?reservation_id=${reservationId}`, '_blank');
              } else {
                  alert('Reservation ID not found.');
              }
              // Close modal after clicking (optional, depends on desired flow)
              modal.classList.add('hidden');
          };
          
          // Re-add event listener for the close button
          document.getElementById('confirmationClose').onclick = () => {
              modal.classList.add('hidden');
          };
      }
    }

    // Static list of countries in French
    const countriesInFrench = [
        "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola", "Antigua-et-Barbuda", 
        "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", 
        "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin", "Bhoutan", "Bolivie", "Bosnie-Herzégovine", 
        "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodge", "Cameroun", 
        "Canada", "Chili", "Chine", "Chypre", "Colombie", "Comores", "Congo", "Congo (République Démocratique du Congo)", 
        "Costa Rica", "Croatie", "Cuba", "Danemark", "Djibouti", "Dominique", "Égypte", "El Salvador", "Équateur", 
        "Érythrée", "Espagne", "Estonie", "Eswatini", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", 
        "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guyana", 
        "Haïti", "Honduras", "Hongrie", "Îles Marshall", "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande", 
        "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Kiribati", 
        "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", "Libye", "Liechtenstein", "Lituanie", "Luxembourg", 
        "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Maurice", "Mauritanie", "Mexique", 
        "Micronésie", "Moldavie", "Monaco", "Mongolie", "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", "Niger", 
        "Nigeria", "Niue", "Norvège", "Nouvelle-Zélande", "Oman", "Ouganda", "Pakistan", "Palaos", "Panama", "Papouasie-Nouvelle-Guinée", 
        "Paraguay", "Pays-Bas", "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "République Démocratique du Congo", 
        "République Dominicaine", "Roumanie", "Royaume-Uni", "Russie", "Rwanda", "Saint-Kitts-et-Nevis", "Saint-Marin", 
        "Saint-Vincent-et-les-Grenadines", "Salvador", "Samoa", "Sao Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles", 
        "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Solomon", "Somalie", "Soudan", "Soudan du Sud", "Sri Lanka", 
        "Suède", "Suisse", "Syrie", "Tadjikistan", "Tanzanie", "Tchad", "Thaïlande", "Togo", "Trinité-et-Tobago", "Tunisie", 
        "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay", "Vanuatu", "Vatican", "Venezuela", "Vietnam", "Yémen", 
        "Zambie", "Zimbabwe"
    ];

    // Function to fetch suggestions from the static list
    function fetchSuggestions(query) {
        if (!query) {
            document.getElementById('suggestions').classList.add('hidden');
            return;
        }

        const filteredCountries = countriesInFrench.filter(country => 
            country.toLowerCase().includes(query.toLowerCase())
        );

        const suggestionsDiv = document.getElementById('suggestions');
        if (filteredCountries.length > 0) {
            suggestionsDiv.classList.remove('hidden');
            suggestionsDiv.innerHTML = filteredCountries.map(country => 
                `<div class="p-2 cursor-pointer" onclick="selectSuggestion('${country}')">${country}</div>`
            ).join('');
        } else {
            suggestionsDiv.classList.add('hidden');
        }
    }

    // Function to handle suggestion selection
    function selectSuggestion(suggestion) {
        document.getElementById('search').value = suggestion;
        document.getElementById('suggestions').classList.add('hidden');
    }

    // Validate that at least one field is filled
    function validateForm() {
        const searchField = document.getElementById('search').value;
        const continentField = document.querySelector('select[name="continent"]').value;
        const maxPriceField = document.querySelector('input[name="max_price"]').value;

        if (!searchField && !continentField && !maxPriceField) {
            alert('Veuillez remplir au moins un champ pour effectuer la recherche.');
            return false;
        }
        return true;
    }

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
            console.log('Attempting to load notifications...');
            const response = await fetch('get_notifications.php');
            console.log('Raw notification response:', response);
            const data = await response.json();
            
            console.log('Parsed notification data:', data);
            
            if (data.success) {
                console.log('Notifications fetched:', data.notifications);
                displayNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    // Display notifications
    function displayNotifications(notifications) {
        console.log('Displaying notifications:', notifications);
        currentNotifications = notifications; // Store the fetched notifications

        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="p-4 text-center text-gray-500 italic">
                    Aucune notification pour le moment.
                </div>
            `;
            notificationCount.classList.add('hidden');
            return;
        }

        // Filter for unread notifications to display count
        const unreadNotifications = notifications.filter(notif => notif.est_lue === 0);
        notificationCount.textContent = unreadNotifications.length; // Display count of unread
        notificationCount.classList.toggle('hidden', unreadNotifications.length === 0);

        notificationList.innerHTML = notifications.map(notification => `
            <div class="p-3 border-b border-gray-200 last:border-b-0 hover:bg-gray-100 transition-colors cursor-pointer rounded-md m-1 ${notification.est_lue ? 'text-gray-500 font-normal' : 'text-gray-800 font-semibold'}" data-notification-id="${notification.id}">
                <div class="flex items-center gap-3">
                    ${notification.est_lue === 0 ? '<span class="w-2 h-2 bg-indigo-600 rounded-full flex-shrink-0"></span>' : '<span class="w-2 h-2 flex-shrink-0"></span>'} <!-- Unread indicator -->
                    <div class="flex-1">
                        <p class="text-sm leading-snug">${notification.message}</p>
                        <span class="text-xs text-gray-500 mt-1 block">${notification.date_envoi}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Check for new notifications every minute
    setInterval(loadNotifications, 60000);

    // Mark all notifications as read
    async function markAllNotificationsAsRead() {
        const notificationElements = notificationList.querySelectorAll('> div');
        if (notificationElements.length === 0) {
            console.log('No notifications to mark as read.');
            return;
        }

        const notificationIds = [];
        notificationElements.forEach(el => {
            // Assuming the notification ID can be extracted from the element's structure or a data attribute
            // For now, we don't have the ID in the HTML, we'll need to adjust displayNotifications later.
            // *** TODO: Get notification IDs from displayed elements ***
        });

        // *** TEMPORARY: For now, we'll assume all fetched notifications should be marked as read ***
        // In a real app, you'd get the IDs from the rendered HTML elements.
        // We need to store the fetched notifications in a variable accessible here.

        // Since we don't have the IDs readily available in the DOM yet, we'll need to refetch or store them.
        // Let's modify loadNotifications to store the fetched notifications and then use those IDs here.
        console.log('Marking all currently loaded notifications as read...');

        // We need the actual notification IDs from the last fetch.
        // Assuming the last fetched notifications are stored in a variable `currentNotifications`
        if (!window.currentNotifications || window.currentNotifications.length === 0) {
            console.log('No loaded notifications to mark as read.');
            return;
        }

        const idsToMark = window.currentNotifications.map(notif => notif.id);

        try {
            const response = await fetch('mark_notifications_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ notification_ids: idsToMark })
            });

            const result = await response.json();

            if (result.success) {
                console.log('Notifications marked as read successfully.');
                // Clear displayed notifications
                notificationList.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        Aucune notification
                    </div>
                `;
                // Hide notification count
                notificationCount.classList.add('hidden');
                // Optionally, reload notifications after a short delay to show only unread ones
                // setTimeout(loadNotifications, 1000);
            } else {
                console.error('Failed to mark notifications as read:', result.message);
            }
        } catch (error) {
            console.error('Error marking notifications as read:', error);
        }
    }

    // Add event listener to the mark all as read button
    document.getElementById('markAllReadBtn').addEventListener('click', markAllNotificationsAsRead);

    // Modify displayNotifications to store fetched notifications
    let currentNotifications = []; // Variable to store currently displayed notifications

    function displayNotifications(notifications) {
        console.log('Displaying notifications:', notifications);
        currentNotifications = notifications; // Store the fetched notifications

        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    Aucune notification
                </div>
            `;
            notificationCount.classList.add('hidden');
            return;
        }

        // Filter for unread notifications to display count
        const unreadNotifications = notifications.filter(notif => notif.est_lue === 0);
        notificationCount.textContent = unreadNotifications.length; // Display count of unread
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

    // Modify loadNotifications to use the updated displayNotifications
    async function loadNotifications() {
        try {
            console.log('Attempting to load notifications...');
            const response = await fetch('get_notifications.php');
            console.log('Raw notification response:', response);
            const data = await response.json();
            
            console.log('Parsed notification data:', data);
            
            if (data.success) {
                console.log('Notifications fetched:', data.notifications);
                displayNotifications(data.notifications); // Use the modified display function
            } else {
                 console.error('Failed to load notifications:', data.message);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    // Initial load of notifications when the page loads
    loadNotifications();

    // Check for new notifications every minute
    setInterval(loadNotifications, 60000);
  </script>
</body>
</html>
