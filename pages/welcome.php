<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agence de Voyage</title>
    <?php
    session_start();
    ?>
    <script src="welcome.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <?php
// Assuming you're connected to the database
require_once '../class/Database.php'; // Include your Database connection file

// Connect to the database
$db = new Database('localhost', 'root', '', 'pfe'); // Modify the connection params as needed
$conn = $db->getConnection();

// Query to get distinct continents
$sql = "SELECT DISTINCT continent FROM pays";
$stmt = $conn->prepare($sql);
$stmt->execute();
$continents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();

// Images fictives par destination
$imageMap = [
    'France' => 'https://upload.wikimedia.org/wikipedia/commons/e/e6/Paris_Night.jpg',
    'États-Unis' => 'https://images.unsplash.com/photo-1598344689264-30ed6f21055c?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
];
?>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        purple: {
                            DEFAULT: '#6B46C1',
                            light: '#9F7AEA',
                            dark: '#553C9A',
                        },
                        mint: {
                            DEFAULT: '#10B981',
                            light: '#34D399',
                            dark: '#059669',
                        },
                        teal: {
                            DEFAULT: '#0D9488',
                            light: '#2DD4BF',
                            dark: '#0F766E',
                        },
                        slate: {
                            DEFAULT: '#64748B',
                            light: '#94A3B8',
                            dark: '#475569',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Slider styles */
        .slider {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            background-size: cover;
            background-position: center;
            pointer-events: none;
        }
        
        .slide.active {
            opacity: 1;
            pointer-events: auto;
        }
        
        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        /* Slide navigation dots */
        .slide-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 30;
        }
        
        .slide-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .slide-dot:hover {
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .slide-dot.active {
            background-color: white;
            transform: scale(1.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Modern Search Bar Styles */
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .search-input {
            background: transparent;
            border: none;
            font-size: 0.95rem;
            color: #2D3748;
            width: 100%;
            padding: 0.5rem 0;
        }

        .search-input:focus {
            outline: none;
        }

        .search-input::placeholder {
            color: #A0AEC0;
        }

        .search-icon {
            color: #6B46C1;
            font-size: 1rem;
            opacity: 0.8;
        }

        .search-button {
            background: #6B46C1;
            color: white;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .search-button:hover {
            background: #553C9A;
            transform: translateY(-1px);
        }

        .search-divider {
            width: 1px;
            background: rgba(107, 70, 193, 0.1);
            margin: 0 0.5rem;
        }
    </style>

</head>
<body class="bg-white/20">
    <!-- Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 bg-transparent backdrop-blur-md z-50 px-6 md:px-12 py-2">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="../images/LOGO.png" alt="PFE Logo" class="h-16 md:h-20 -my-4">
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-6">
                <a href="#" class="text-teal font-semibold hover:text-teal-dark transition-colors">Home</a>
                <a href="Discover.php" class="text-slate font-semibold hover:text-teal transition-colors">Discover</a>
                <a href="#" class="text-slate font-semibold hover:text-teal transition-colors">Services</a>
                <a href="#" class="text-slate font-semibold hover:text-teal transition-colors">News</a>
                <a href="#" class="text-slate font-semibold hover:text-teal transition-colors">Contact</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-slate font-semibold hover:text-teal transition-colors flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="Login.php" class="text-slate font-semibold hover:text-teal transition-colors">Join us</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- top Section with Image Slider -->
    <section class="relative h-screen">
        <!-- Image Slider -->
        <div class="slider">
            <div class="slide" style="background-image: url('../images/background1.jpg');">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide" style="background-image: url('../images/background2.jpg');">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide" style="background-image: url('../images/background3.jpg');">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide" style="background-image: url('../images/background4.jpg');">
                <div class="slide-overlay"></div>
            </div>
            
            <!-- Slide navigation dots -->
            <div class="slide-dots">
                <div class="slide-dot active"></div>
                <div class="slide-dot"></div>
                <div class="slide-dot"></div>
                <div class="slide-dot"></div>
            </div>
        </div>
        
        <!-- Top Content -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center z-10">
            <h1 class="text-4xl md:text-5xl font-bold text-white px-4">Explore the world with a smile</h1>
            <p class="text-white mt-4 w-4/5 md:w-1/2 px-4">Discover new destinations, create unforgettable memories, and travel with confidence.</p>

<!-- Search Section -->
<div class="w-full max-w-4xl mx-auto px-4 mt-8">
    <div class="search-container p-4 flex flex-wrap items-center gap-4">
        <form action="destinations.php" method="GET" class="w-full flex flex-wrap gap-4 items-center" onsubmit="return validateForm()">
            <div class="flex-1 flex items-center gap-2 relative">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       id="search" 
                       name="search" 
                       class="search-input"
                       placeholder="Où voulez-vous aller?"
                       autocomplete="off"
                       oninput="fetchSuggestions(this.value)">
                
                <!-- Suggestions box -->
                <div id="suggestions" class="absolute z-10 w-full top-full mt-2 bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <i class="fas fa-globe search-icon"></i>
                <select name="continent" class="search-input">
                    <option value="">Tous les continents</option>
                    <?php foreach ($continents as $cont): ?>
                        <option value="<?php echo htmlspecialchars($cont['continent']); ?>">
                            <?php echo htmlspecialchars($cont['continent']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <i class="fas fa-euro-sign search-icon"></i>
                <input type="number" 
                       name="max_price" 
                       class="search-input" 
                       placeholder="Prix maximum">
            </div>

            <button type="submit" class="search-button">
                Rechercher
            </button>
        </form>
    </div>
</div>

<script>
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
</script>

        </div>
    </section>
    
    <!-- Featured Destinations -->
    <section class="py-16 px-6 max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12 text-purple">Popular Destinations</h2>
        <div class="relative">
            <!-- Destination Cards Container -->
            <div class="destination-carousel relative h-[500px] overflow-hidden">
                <!-- Destination Card 1 -->
                <div class="destination-card absolute w-full max-w-md transition-all duration-500 ease-in-out" style="left: 50%; transform: translateX(-50%);">
                    <div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-200 bg-white">
                        <div class="h-64 bg-cover bg-center" style="background-image: url('../images/card1.jpg');"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-semibold text-purple">Paris, France</h3>
                                <span class="text-teal font-bold">13,000,0 DZD</span>
                            </div>
                            <p class="text-gray-600 mb-4">Experience the romance and beauty of the City of Light.</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-teal"></i>
                                    <span class="ml-1">4.8 (243 reviews)</span>
                                </div>
                                <span class="text-gray-500">7 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Card 2 -->
                <div class="destination-card absolute w-full max-w-md transition-all duration-500 ease-in-out" style="left: 50%; transform: translateX(-50%);">
                    <div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-200 bg-white">
                        <div class="h-64 bg-cover bg-center" style="background-image: url('../images/card2.jpg');"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-semibold text-purple">Bali, Indonesia</h3>
                                <span class="text-teal font-bold">12,500,0 DZD</span>
                            </div>
                            <p class="text-gray-600 mb-4">Tropical paradise with stunning beaches and vibrant culture.</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-teal"></i>
                                    <span class="ml-1">4.9 (517 reviews)</span>
                                </div>
                                <span class="text-gray-500">10 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Card 3 -->
                <div class="destination-card absolute w-full max-w-md transition-all duration-500 ease-in-out" style="left: 50%; transform: translateX(-50%);">
                    <div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-200 bg-white">
                        <div class="h-64 bg-cover bg-center" style="background-image: url('../images/card3.jpg');"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-semibold text-purple">Santorini, Greece</h3>
                                <span class="text-teal font-bold">15,000,0 DZD</span>
                            </div>
                            <p class="text-gray-600 mb-4">Breathtaking views and iconic white and blue architecture.</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-teal"></i>
                                    <span class="ml-1">4.7 (326 reviews)</span>
                                </div>
                                <span class="text-gray-500">8 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Card 4 -->
                <div class="destination-card absolute w-full max-w-md transition-all duration-500 ease-in-out" style="left: 50%; transform: translateX(-50%);">
                    <div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-200 bg-white">
                        <div class="h-64 bg-cover bg-center" style="background-image: url('../images/card4.jpg');"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-semibold text-purple">Cairo, Egypt</h3>
                                <span class="text-teal font-bold">11,500,0 DZD</span>
                            </div>
                            <p class="text-gray-600 mb-4">Discover the ancient wonders and rich history of the Nile.</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-teal"></i>
                                    <span class="ml-1">4.8 (412 reviews)</span>
                                </div>
                                <span class="text-gray-500">6 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Card 5 -->
                <div class="destination-card absolute w-full max-w-md transition-all duration-500 ease-in-out" style="left: 50%; transform: translateX(-50%);">
                    <div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow border border-gray-200 bg-white">
                        <div class="h-64 bg-cover bg-center" style="background-image: url('../images/card5.jpg');"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-semibold text-purple">Oran, Algeria</h3>
                                <span class="text-teal font-bold">8,500,0 DZD</span>
                            </div>
                            <p class="text-gray-600 mb-4">Experience the Mediterranean charm and cultural heritage.</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-teal"></i>
                                    <span class="ml-1">4.6 (289 reviews)</span>
                                </div>
                                <span class="text-gray-500">5 days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button id="prevDestination" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-purple rounded-full p-3 z-20 transition-all shadow-lg">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button id="nextDestination" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-purple rounded-full p-3 z-20 transition-all shadow-lg">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>

            <!-- Card Indicators -->
            <div class="flex justify-center space-x-2 mt-6">
                <button class="destination-dot w-3 h-3 rounded-full bg-purple/30 hover:bg-purple/50 transition-colors"></button>
                <button class="destination-dot w-3 h-3 rounded-full bg-purple/30 hover:bg-purple/50 transition-colors"></button>
                <button class="destination-dot w-3 h-3 rounded-full bg-purple/30 hover:bg-purple/50 transition-colors"></button>
                <button class="destination-dot w-3 h-3 rounded-full bg-purple/30 hover:bg-purple/50 transition-colors"></button>
                <button class="destination-dot w-3 h-3 rounded-full bg-purple/30 hover:bg-purple/50 transition-colors"></button>
            </div>
        </div>
    </section>

    
    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-purple">Why Choose Us</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-mint/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-globe text-mint text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-purple">Best Price Guarantee</h3>
                    <p class="text-gray-600">We promise you'll get the best prices on all our packages and destinations.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-mint/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-mint text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-purple">24/7 Customer Support</h3>
                    <p class="text-gray-600">Our friendly team is here to help you anytime, anywhere during your travels.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-mint/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marked-alt text-mint text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-purple">Handpicked Destinations</h3>
                    <p class="text-gray-600">We personally select and verify all locations for quality and authenticity.</p>
                </div>
            </div>
        </div>
    </section>
    
<!-- Reviews -->
<section class="py-16 px-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12 text-purple">What Our Travelers Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Review 1 -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <div class="flex items-center space-x-1 mb-4 text-teal">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star-half-alt"></i>
    </div>
    <p class="text-gray-600 italic mb-4">"The trip to Morocco was absolutely incredible. Everything was perfectly organized, from the accommodations to the guided tours. We'll definitely book with PFE for our next adventure!"</p>
    <div class="flex items-center">
        <div class="w-12 h-12 rounded-full bg-cover bg-center mr-4" style="background-image: url('../images/face.png');"></div>
        <div>
            <h4 class="font-semibold text-purple">Moncef Kameli</h4>
            <p class="text-sm text-gray-500">Traveled to Morocco</p>
        </div>
    </div>
</div>

            <!-- Review 2 -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-center space-x-1 mb-4 text-teal">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 italic mb-4">"Our family trip to Thailand exceeded all expectations. The guides were knowledgeable, friendly, and made sure we experienced the authentic culture. The children still talk about it!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-cover bg-center mr-4" style="background-image: url('../images/face.png');"></div>
                    <div>
                        <h4 class="font-semibold text-purple">Adel Chibah</h4>
                        <p class="text-sm text-gray-500">Traveled to Thailand</p>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-center space-x-1 mb-4 text-teal">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 italic mb-4">"I was nervous about solo travel, but the team at PFE made it so easy and comfortable. I felt safe and supported throughout my entire journey in Japan. Already planning my next trip!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-cover bg-center mr-4" style="background-image: url('../images/face.png');"></div>
                    <div>
                        <h4 class="font-semibold text-purple">Khaled Ferhaoui</h4>
                        <p class="text-sm text-gray-500">Traveled to Japan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    
    <!-- Newsletter -->
    <section class="py-16 px-6 bg-gradient-to-r from-purple to-purple-dark text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Get Exclusive Travel Offers</h2>
            <p class="mb-8 text-white/80">Subscribe to our newsletter and be the first to know about special discounts, new destinations, and travel tips.</p>
            <form class="flex flex-col md:flex-row max-w-lg mx-auto">
                <input type="email" placeholder="Your email address" class="px-4 py-3 w-full rounded-l-lg md:rounded-r-none rounded-r-lg mb-3 md:mb-0 focus:outline-none text-gray-900">
                <button class="bg-mint text-white px-6 py-3 rounded-r-lg md:rounded-l-none rounded-l-lg font-semibold hover:bg-mint-dark transition-colors">
                    Subscribe Now
                </button>
            </form>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="../images/LOGO.png" alt="PFE Logo" class="h-16 md:h-20 -my-4">
                    <p class="text-gray-300">Your trusted travel partner since 2025. We make your dream vacations come true.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-300 hover:text-mint transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-300 hover:text-mint transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-mint transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-mint transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-mint transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-mint transition-colors">Destinations</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-mint transition-colors">Tours</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-mint transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
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
                    <p class="text-gray-300 mb-4">Subscribe to our newsletter for the latest updates and offers.</p>
                    <form class="flex">
                        <input type="email" placeholder="Your email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-gray-900">
                        <button class="bg-mint px-4 py-2 rounded-r-lg hover:bg-mint-dark transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>













            
            
            <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400">
                <p>&copy; 2025 PFE Travels. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
