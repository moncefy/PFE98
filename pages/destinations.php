<?php
// Assuming you're connected to the database
require_once '../class/Database.php'; // Include your Database connection file

// Connect to the database
$db = new Database('localhost', 'root', '', 'pfe'); // Modify the connection params as needed
$conn = $db->getConnection();

// Get the search query, continent, and maximum price from GET parameters
$searchQuery = trim($_GET['search'] ?? '');
$continent = $_GET['continent'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

// Start building the SQL query for the search
$sql = "SELECT id, numero_vol, compagnie_aerienne, aeroport_depart, aeroport_arrivee, 
               destination, continent, date_depart, date_arrivee, type_vol, prix, places_disponibles
        FROM vol
        WHERE 1"; // Always true for base condition

$params = [];

// If a destination is provided, add the condition to filter by destination
if ($searchQuery) {
    $sql .= " AND destination LIKE ?";
    $params[] = "%$searchQuery%";
}

// If a continent is selected, add that condition
if ($continent) {
    $sql .= " AND continent = ?";
    $params[] = $continent;
}

// If a maximum price is provided, add that condition
if ($maxPrice) {
    $sql .= " AND prix <= ?";
    $params[] = $maxPrice;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params); // Bind all parameters dynamically
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();
?>

<!-- Display the results on destinations.php -->
<div class="search-results">
    <?php if ($results): ?>
        <ul>
            <?php foreach ($results as $flight): ?>
                <li>
                    <strong>Vol: <?php echo htmlspecialchars($flight['numero_vol']); ?></strong><br>
                    <strong>Compagnie:</strong> <?php echo htmlspecialchars($flight['compagnie_aerienne']); ?><br>
                    <strong>Aéroport départ:</strong> <?php echo htmlspecialchars($flight['aeroport_depart']); ?><br>
                    <strong>Aéroport arrivée:</strong> <?php echo htmlspecialchars($flight['aeroport_arrivee']); ?><br>
                    <strong>Destination:</strong> <?php echo htmlspecialchars($flight['destination']); ?><br>
                    <strong>Date départ:</strong> <?php echo htmlspecialchars($flight['date_depart']); ?><br>
                    <strong>Date arrivée:</strong> <?php echo htmlspecialchars($flight['date_arrivee']); ?><br>
                    <strong>Prix:</strong> €<?php echo htmlspecialchars($flight['prix']); ?><br>
                    <strong>Places disponibles:</strong> <?php echo htmlspecialchars($flight['places_disponibles']); ?><br>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun vol trouvé pour cette recherche.</p>
    <?php endif; ?>
</div>
