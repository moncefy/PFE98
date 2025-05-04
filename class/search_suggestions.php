<?php
// search_suggestions.php

// Database connection (modify this according to your DB credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name"; // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare the SQL query to fetch country names that match the query
$sql = "SELECT nom FROM pays WHERE nom LIKE ? LIMIT 10";  // You can adjust the LIMIT to fetch more suggestions
$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$stmt->bind_result($country);

// Collect results
$countries = [];
while ($stmt->fetch()) {
    $countries[] = $country;
}

$stmt->close();

// Return the countries as JSON
echo json_encode($countries);

// Close connection
$conn->close();
?>
