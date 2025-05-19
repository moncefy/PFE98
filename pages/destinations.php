<?php
// destinations.php
require_once '../class/Database.php';

// Connexion à la base
$db   = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

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
    $sql      .= " AND continent = ?";
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
    'France' => 'https://upload.wikimedia.org/wikipedia/commons/e/e6/Paris_Night.jpg',
    'États-Unis' => 'https://upload.wikimedia.org/wikipedia/commons/1/16/The_White_House_%28cropped%29.jpg',
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
  <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-50">
    <div class="container mx-auto flex justify-between items-center p-4">
      <div class="space-x-4">
        <a href="welcome.php" class="text-gray-600 hover:text-gray-800">Accueil</a>
        <a href="#" class="text-gray-600 hover:text-gray-800">Découvrir</a>
      </div>
      <a href="welcome.php" class="text-indigo-600">← Rechercher</a>
    </div>
  </nav>

  <main class="mt-20 max-w-7xl mx-auto p-4">
    <!-- Filtres dynamiques -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow flex flex-col md:flex-row gap-4">
      <input id="filterSearch" type="text" placeholder="Rechercher destination..." class="border rounded p-2 flex-1" />
      <select id="filterContinent" class="border rounded p-2">
        <option value="">Tous les continents</option>
        <option value="europe">Europe</option>
        <option value="amerique">Amérique</option>
        <option value="asie">Asie</option>
      </select>
      <input id="filterPrice" type="number" placeholder="Prix max DZD" class="border rounded p-2 w-40" />
    </div>

    <?php if (empty($results)): ?>
      <p class="text-center text-gray-500">Aucun vol trouvé.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($results as $vol): 
          $dest = ucfirst(strtolower($vol['destination']));
          $img  = $imageMap[$dest] ?? 'https://via.placeholder.com/400x300?text=' . urlencode($dest);
        ?>
        <div class="vol-card bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col"
             data-dest="<?= htmlspecialchars($vol['destination']) ?>"
             data-continent="<?= htmlspecialchars(strtolower($vol['continent'])) ?>"
             data-price="<?= $vol['prix'] ?>">
          <div class="h-40 bg-cover bg-center" style="background-image:url('<?= $img ?>')"></div>
          <div class="p-4 flex-1 flex flex-col">
            <h3 class="text-indigo-600 font-bold text-xl mb-2">Vol : <?= $vol['numero_vol'] ?></h3>
            <p class="text-gray-700"><strong>Compagnie :</strong> <?= $vol['compagnie_aerienne'] ?></p>
            <p class="text-gray-700"><strong>Départ :</strong> <?= $vol['aeroport_depart'] ?></p>
            <p class="text-gray-700"><strong>Arrivée :</strong> <?= $vol['aeroport_arrivee'] ?></p>
            <p class="text-gray-700"><strong>Dest. :</strong> <?= $vol['destination'] ?></p>
            <p class="text-gray-700"><strong>Date :</strong> <?= date('d/m H:i', strtotime($vol['date_depart'])) ?></p>
            <p class="text-indigo-600 font-semibold mt-auto">DZD<?= number_format($vol['prix'],2,',',' ') ?></p>
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

  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-11/12 md:w-2/3 lg:w-1/2 relative">
      <button onclick="closeModal()" class="absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
      <div id="modalBody"></div>
    </div>
  </div>

  <script>
    const hotels = <?= json_encode($hotels) ?>;

    function openModal(flight) {
      document.getElementById('modal').classList.remove('hidden');
      let filteredHotels = hotels.filter(h => h.pays?.toLowerCase() === flight.destination.toLowerCase());
      let hotelOptions = filteredHotels.length
        ? filteredHotels.map(h => `<option value="${h.id}" data-price="${h.prix_par_nuit}" data-type="${h.type_chambre}" data-dispo="${h.chambres_disponible}">${h.nom} (${h.etoiles} ⭐) - DZD${h.prix_par_nuit}</option>`).join('')
        : `<option disabled>Aucun hôtel disponible à cette destination</option>`;

      const html = `
        <h2 class="text-2xl font-bold text-indigo-700 mb-4">Réservation Vol ${flight.numero_vol}</h2>
        <p><strong>Départ :</strong> ${flight.aeroport_depart}</p>
        <p><strong>Arrivée :</strong> ${flight.aeroport_arrivee}</p>
        <p><strong>Date départ :</strong> ${new Date(flight.date_depart).toLocaleString()}</p>
        <p><strong>Prix vol :</strong> DZD${parseFloat(flight.prix).toLocaleString('fr-FR')}</p>
        <hr class="my-4"/>

        <label class="flex items-center gap-2">
          <input type="checkbox" id="hotelCB"> Ajouter hôtel
        </label>
        <div id="hotelSection" class="mt-4 hidden">
          <select id="hotelSel" class="w-full border p-2 rounded">
            <option value="">-- Choisir un hôtel --</option>
            ${hotelOptions}
          </select>
          <div class="mt-2">
            <label class="block text-sm font-medium text-gray-700">Nombre de chambres:</label>
            <input type="number" id="numRoomsInput" min="1" value="1" class="w-full border p-2 rounded mt-1">
          </div>
          <div class="mt-2">
            <label class="block text-sm font-medium text-gray-700">Type de chambre:</label>
            <select id="roomTypeSel" class="w-full border p-2 rounded mt-1">
              <option value="simple">Simple</option>
              <option value="double">Double</option>
              <option value="suite">Suite</option>
              <option value="familiale">Familiale</option>
            </select>
          </div>
          <div class="mt-2">
            <label class="block text-sm font-medium text-gray-700">Date de début (hôtel):</label>
            <input type="date" id="dateDebutHotelInput" class="w-full border p-2 rounded mt-1">
          </div>
          <div class="mt-2">
            <label class="block text-sm font-medium text-gray-700">Date de fin (hôtel):</label>
            <input type="date" id="dateFinInput" class="w-full border p-2 rounded mt-1">
          </div>
          <p id="hotelInfo" class="mt-2 text-indigo-600 font-medium"></p>
        </div>

        <label class="block mt-4">Transport :</label>
        <select id="transportSel" class="w-full border p-2 rounded">
          <option value="0">-- Aucun --</option>
          <option value="2500">Bus (+2500 DZD)</option>
          <option value="5000">Taxi (+5000 DZD)</option>
          <option value="3000">Train (+3000 DZD)</option>
        </select>

        <p id="totalPrice" class="mt-4 font-bold text-green-600 text-lg">Total : DZD${parseFloat(flight.prix).toLocaleString('fr-FR')}</p>

        <button onclick='confirmBooking(${JSON.stringify(flight)})'
          class="mt-6 bg-green-600 text-white py-2 rounded-lg w-full hover:bg-green-700">
          Réserver
        </button>
      `;

      document.getElementById('modalBody').innerHTML = html;

      const basePrice = parseFloat(flight.prix);
      let hotelPrice = 0;
      let transportCost = 0;

      const updateTotal = () => {
        const total = basePrice + hotelPrice + transportCost;
        document.getElementById('totalPrice').textContent = `Total : DZD${total.toLocaleString('fr-FR')}`;
      };

      document.getElementById('hotelCB').addEventListener('change', e => {
        document.getElementById('hotelSection').classList.toggle('hidden', !e.target.checked);
        if (!e.target.checked) {
          hotelPrice = 0;
          updateTotal();
        }
      });

      document.getElementById('hotelSel').addEventListener('change', e => {
        const sel = e.target.selectedOptions[0];
        hotelPrice = sel.dataset.price ? parseFloat(sel.dataset.price) : 0;
        const dispo = sel.dataset.dispo;
        const type = sel.dataset.type;
        document.getElementById('hotelInfo').textContent = dispo && type ? `${dispo} chambres disponibles` : '';
        updateTotal();
      });

      document.getElementById('transportSel').addEventListener('change', e => {
        transportCost = parseFloat(e.target.value);
        updateTotal();
      });
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

      if (hotelSelected) {
        const hotelSel = document.getElementById('hotelSel');
        const selectedOption = hotelSel.selectedOptions[0];
        if (selectedOption && selectedOption.value !== "") {
          hotelName = selectedOption.text.split(' (')[0].trim(); // Extract name before the star rating
          nombreChambres = parseInt(document.getElementById('numRoomsInput').value, 10);
          typeChambre = document.getElementById('roomTypeSel').value;
          dateDebutHotel = document.getElementById('dateDebutHotelInput').value;
          dateFin = document.getElementById('dateFinInput').value;
        }
      }

      const transportCost = parseFloat(document.getElementById('transportSel').value);
      const totalAmount = parseFloat(document.getElementById('totalPrice').textContent.replace('Total : DZD', '').replace(/\s/g, '').replace(',', '.')); // Parse the total price

      const reservationData = {
        numero_vol: flight.numero_vol,
        nom_hotel: hotelName, // Will be null if no hotel selected
        nombre_chambres: nombreChambres, // Will be 0 if no hotel selected
        type_chambre: typeChambre, // Will be null if no hotel selected
        date_debut_hotel: dateDebutHotel, // From modal input
        date_fin_hotel: dateFin, // From modal input
        // client_id: // TODO: Get actual client ID from session/auth
        montant_total: totalAmount,
        // statut will be set on server ('En Cours')
        // date_reservation will be set on server (current date)
        // est_paye will be set on server (non payé)
      };

      console.log('Reservation Data:', reservationData);

      // Send data to server
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
          alert('Réservation confirmée avec succès ! ✅');
          closeModal();
          // Optionally refresh the page or update the flight list
        } else {
          alert('Erreur lors de la confirmation de la réservation: ' + result.message);
        }
      } catch (error) {
        console.error('Error during reservation:', error);
        alert('Une erreur est survenue lors de la réservation.');
      }
    }

    // Filtrage dynamique
    function normalize(text) {
      return text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }
    function filterCards() {
      const search = normalize(document.getElementById('filterSearch').value);
      const continent = normalize(document.getElementById('filterContinent').value);
      const maxPrice = parseFloat(document.getElementById('filterPrice').value);

      document.querySelectorAll('.vol-card').forEach(card => {
        const dest = normalize(card.dataset.dest || '');
        const cont = normalize(card.dataset.continent || '');
        const price = parseFloat(card.dataset.price || 0);

        const matchesSearch = !search || dest.includes(search);
        const matchesContinent = !continent || cont === continent;
        const matchesPrice = !maxPrice || price <= maxPrice;

        card.classList.toggle('hidden', !(matchesSearch && matchesContinent && matchesPrice));
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      ['filterSearch', 'filterContinent', 'filterPrice'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', filterCards);
      });
    });
  </script>
</body>
</html>
