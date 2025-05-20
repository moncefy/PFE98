<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

// Discover.php
require_once '../class/Database.php';

// Connexion à la base
$db = new Database('localhost', 'root', '', 'pfe');
$conn = $db->getConnection();

// Récupération des filtres GET
$continentFilter   = $_GET['continent'] ?? '';
$destinationFilter = $_GET['destination'] ?? '';

// Récupérer les listes distinctes pour les filtres
$continents = $conn->query("SELECT DISTINCT continent FROM vol ORDER BY continent")->fetch_all(MYSQLI_ASSOC);
$destinations = $conn->query("SELECT DISTINCT destination FROM vol ORDER BY destination")->fetch_all(MYSQLI_ASSOC);

// Construction de la requête SQL principale
$sql    = "SELECT * FROM vol WHERE 1";
$params = [];
$types  = '';
if ($continentFilter) {
    $sql       .= " AND continent = ?";
    $params[]  = $continentFilter;
    $types    .= 's';
}
if ($destinationFilter) {
    $sql       .= " AND destination = ?";
    $params[]  = $destinationFilter;
    $types    .= 's';
}
$sql .= " ORDER BY date_depart ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$flights = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

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
    'USA' => 'https://upload.wikimedia.org/wikipedia/commons/a/a3/Empire_State_Building_from_the_Top_of_the_Rock.jpg',
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
  <title>Découvrir tous les vols</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigation Bar -->
  <nav class="fixed top-0 left-0 right-0 bg-white backdrop-blur-md shadow-md z-50">
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-4 flex justify-between items-center">
      <div class="flex items-center gap-4">
        <a href="welcome.php" class="text-gray-600 hover:text-gray-900 font-semibold">Accueil</a>
        <a href="Discover.php" class="text-indigo-600 font-bold">Découvrir</a>
        <a href="Services.php" class="text-gray-600 hover:text-gray-900 font-semibold">Services</a>
        <a href="Actualites.php" class="text-gray-600 hover:text-gray-900 font-semibold">Actualités</a>
        <a href="Contact.php" class="text-gray-600 hover:text-gray-900 font-semibold">Contact</a>
      </div>
      <a href="welcome.php" class="text-indigo-600 font-bold hover:text-indigo-800">&larr; Retour</a>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="pt-24 max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Tous les vols</h1>

    <!-- Filtres -->
    <form method="GET" class="mb-8 flex flex-wrap gap-4 items-center">
      <div class="flex-1 min-w-[200px]">
        <label class="block text-gray-700 mb-1">Filtrer par continent</label>
        <select name="continent" class="w-full border rounded-lg px-3 py-2">
          <option value="">Tous les continents</option>
          <?php foreach ($continents as $c): ?>
            <option value="<?= htmlspecialchars($c['continent']) ?>" <?= $c['continent'] === $continentFilter ? 'selected' : '' ?> >
              <?= htmlspecialchars($c['continent']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex-1 min-w-[200px]">
        <label class="block text-gray-700 mb-1">Filtrer par destination</label>
        <select name="destination" class="w-full border rounded-lg px-3 py-2">
          <option value="">Toutes les destinations</option>
          <?php foreach ($destinations as $d): ?>
            <option value="<?= htmlspecialchars($d['destination']) ?>" <?= $d['destination'] === $destinationFilter ? 'selected' : '' ?> >
              <?= htmlspecialchars($d['destination']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">Appliquer</button>
    </form>

    <?php if (empty($flights)): ?>
      <div class="text-center py-20">
        <p class="text-gray-500 text-lg">Aucun vol disponible pour ces critères.</p>
      </div>
    <?php else: ?>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($flights as $flight): ?>
          <?php 
            $dest = ucfirst(strtolower($flight['destination']));
            $imgPath = $imageMap[$dest] ?? 'https://via.placeholder.com/400x300?text=' . urlencode($dest);
          ?>
          <div class="bg-white rounded-2xl overflow-hidden shadow hover:shadow-lg transition flex flex-col">
            <div class="h-40 bg-cover bg-center" style="background-image: url('<?= $imgPath ?>')"></div>
            <div class="p-6 flex flex-col flex-1">
              <h2 class="text-xl font-bold text-indigo-600 mb-2">Vol : <?= htmlspecialchars($flight['numero_vol']) ?></h2>
              <p class="text-gray-700 mb-1"><span class="font-medium">Compagnie :</span> <?= htmlspecialchars($flight['compagnie_aerienne']) ?></p>
              <p class="text-gray-700 mb-1"><span class="font-medium">Départ :</span> <?= htmlspecialchars($flight['aeroport_depart']) ?></p>
              <p class="text-gray-700 mb-1"><span class="font-medium">Arrivée :</span> <?= htmlspecialchars($flight['aeroport_arrivee']) ?></p>
              <p class="text-gray-700 mb-1"><span class="font-medium">Destination :</span> <?= htmlspecialchars($flight['destination']) ?></p>
              <p class="text-gray-700 mb-1"><span class="font-medium">Date départ :</span> <?= date('d/m/Y H:i', strtotime($flight['date_depart'])) ?></p>
              <div class="mt-auto">
                <p class="text-indigo-700 text-lg font-semibold">DZD<?= number_format($flight['prix'], 2, ',', ' ') ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="text-center py-6 text-gray-500">
    © <?= date('Y') ?> Votre Agence de Voyage
  </footer>

</body>
</html>
