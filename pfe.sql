-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 19, 2025 at 06:57 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pfe`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `insert_random_vols_150`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_random_vols_150` ()   BEGIN
  DECLARE i INT DEFAULT 0;
  WHILE i < 150 DO
    INSERT INTO vol (
      numero_vol,
      compagnie_aerienne,
      aeroport_depart,
      aeroport_arrivee,
      destination,
      date_depart,
      date_arrivee,
      type_vol,
      prix,
      places_disponibles
    )
    VALUES (
      CONCAT('DZ', LPAD(FLOOR(RAND()*9000+1000),4,'0')),
      ELT(FLOOR(1+RAND()*10),
        'Air Algérie','Tassili Airlines','Turkish Airlines','Qatar Airways',
        'Emirates','Lufthansa','EgyptAir','Etihad Airways',
        'Singapore Airlines','American Airlines'
      ),
      ELT(FLOOR(1+RAND()*6),
        'Aéroport d’Alger - Houari Boumediene',
        'Aéroport d’Oran - Ahmed Ben Bella',
        'Aéroport de Constantine - Mohamed Boudiaf',
        'Aéroport d’Annaba - Rabah Bitat',
        'Aéroport de Tamanrasset - Aguenar',
        'Aéroport de Béjaïa - Soummam'
      ),
      ELT(FLOOR(1+RAND()*50),
        'Paris CDG (France)', 'Istanbul IST (Turquie)', 'Tunis TUN (Tunisie)',
        'Dubaï DXB (É.A.U.)', 'Francfort FRA (Allemagne)', 'Rome FCO (Italie)',
        'Londres LHR (R.-U.)', 'New York JFK (USA)', 'Madrid MAD (Espagne)',
        'Moscou SVO (Russie)', 'Los Angeles LAX (USA)', 'Tokyo NRT (Japon)',
        'Shanghai PVG (Chine)', 'Singapour SIN (Singapour)',
        'Johannesburg JNB (A.-S.)', 'Sydney SYD (Australie)',
        'Hong Kong HKG (R.A.S.)', 'São Paulo GRU (Brésil)',
        'Buenos Aires EZE (Argentine)', 'Mexico City MEX (Mexique)',
        'Lima LIM (Pérou)', 'Toronto YYZ (Canada)', 'Berlin BER (Allemagne)',
        'Barcelone BCN (Espagne)', 'Bangkok BKK (Thaïlande)',
        'Jakarta CGK (Indonésie)', 'Manille MNL (Philippines)',
        'Kuala Lumpur KUL (Malaisie)', 'Riyad RUH (Arabie Saoudite)',
        'Casablanca CMN (Maroc)', 'Tel Aviv TLV (Israël)',
        'Prague PRG (République tchèque)', 'Zurich ZRH (Suisse)',
        'Mumbai BOM (Inde)', 'Cairo CAI (Égypte)', 'Athens ATH (Grèce)',
        'Amsterdam AMS (Pays-Bas)', 'Brussels BRU (Belgique)',
        'Lisbon LIS (Portugal)', 'Dublin DUB (Irlande)',
        'Vienna VIE (Autriche)', 'Oslo OSL (Norvège)',
        'Stockholm ARN (Suède)', 'Helsinki HEL (Finlande)',
        'Copenhagen CPH (Danemark)', 'Warsaw WAW (Pologne)',
        'Budapest BUD (Hongrie)', 'Bucharest OTP (Roumanie)',
        'Nairobi NBO (Kenya)', 'Lagos LOS (Nigeria)'
      ),
      ELT(FLOOR(1+RAND()*50),
        'France','Turquie','Tunisie','Émirats Arabes Unis','Allemagne',
        'Italie','Royaume-Uni','États-Unis','Espagne','Russie',
        'États-Unis','Japon','Chine','Singapour','Afrique du Sud',
        'Australie','Hong Kong','Brésil','Argentine','Mexique',
        'Pérou','Canada','Allemagne','Espagne','Thaïlande',
        'Indonésie','Philippines','Malaisie','Arabie Saoudite','Maroc',
        'Israël','République tchèque','Suisse','Inde','Égypte',
        'Grèce','Pays-Bas','Belgique','Portugal','Irlande',
        'Autriche','Norvège','Suède','Finlande','Danemark',
        'Pologne','Hongrie','Roumanie','Kenya','Nigeria'
      ),
      DATE_ADD('2025-06-01 00:00:00', INTERVAL FLOOR(RAND()*92) DAY)
        + INTERVAL FLOOR(RAND()*24) HOUR
        + INTERVAL FLOOR(RAND()*60) MINUTE,
      DATE_ADD(
        DATE_ADD('2025-06-01 00:00:00', INTERVAL FLOOR(RAND()*92) DAY)
          + INTERVAL FLOOR(RAND()*24) HOUR
          + INTERVAL FLOOR(RAND()*60) MINUTE,
        INTERVAL FLOOR(1+RAND()*7) DAY
      ),
      ELT(FLOOR(1+RAND()*2),'Aller simple','Aller-retour'),
      FLOOR(10000 + RAND()*80000),
      FLOOR(30 + RAND()*220)
    );
    SET i = i + 1;
  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `niveau` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `niveau`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `reservation_id` int DEFAULT NULL,
  `stars` float DEFAULT NULL,
  `commentaire` text,
  `date_avis` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `reservation_id` (`reservation_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `paysId` int NOT NULL,
  `population` int DEFAULT NULL,
  `isCapital` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_city_nom` (`nom`),
  KEY `idx_city_pays` (`paysId`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `nom`, `paysId`, `population`, `isCapital`, `created_at`, `updated_at`) VALUES
(1, 'Paris', 1, 2161000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(2, 'Marseille', 1, 870000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(3, 'Lyon', 1, 513000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(4, 'Toulouse', 1, 471000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(5, 'Nice', 1, 342000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(6, 'Berlin', 2, 3664000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(7, 'Hambourg', 2, 1841000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(8, 'Munich', 2, 1488000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(9, 'Cologne', 2, 1086000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(10, 'Francfort', 2, 753000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(11, 'Rome', 3, 2873000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(12, 'Milan', 3, 1378000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(13, 'Naples', 3, 967000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(14, 'Turin', 3, 886000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(15, 'Palerme', 3, 676000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(16, 'Madrid', 4, 3223000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(17, 'Barcelone', 4, 1620000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(18, 'Valence', 4, 791000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(19, 'Séville', 4, 688000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(20, 'Zaragoza', 4, 675000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(21, 'Rabat', 21, 577000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(22, 'Casablanca', 21, 3350000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(23, 'Fès', 21, 1150000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(24, 'Marrakech', 21, 928000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(25, 'Tanger', 21, 947000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(26, 'Alger', 22, 3416000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(27, 'Oran', 22, 803000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(28, 'Constantine', 22, 448000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(29, 'Annaba', 22, 342000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(30, 'Blida', 22, 331000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(31, 'Tunis', 23, 693000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(32, 'Sfax', 23, 277000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(33, 'Sousse', 23, 221000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(34, 'Kairouan', 23, 139000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(35, 'Bizerte', 23, 136000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(36, 'Washington', 36, 705000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(37, 'New York', 36, 8337000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(38, 'Los Angeles', 36, 3897000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(39, 'Chicago', 36, 2746000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(40, 'Houston', 36, 2304000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(41, 'Ottawa', 37, 994000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(42, 'Toronto', 37, 2732000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(43, 'Montréal', 37, 1705000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(44, 'Vancouver', 37, 631000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(45, 'Calgary', 37, 1239000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(46, 'Pékin', 52, 20896000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(47, 'Shanghai', 52, 24280000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(48, 'Guangzhou', 52, 16300000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(49, 'Shenzhen', 52, 13430000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(50, 'Chengdu', 52, 16500000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(51, 'Tokyo', 53, 37390000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(52, 'Yokohama', 53, 3726000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(53, 'Osaka', 53, 2668000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(54, 'Nagoya', 53, 2283000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(55, 'Sapporo', 53, 1952000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(56, 'Canberra', 69, 431000, 1, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(57, 'Sydney', 69, 5367000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(58, 'Melbourne', 69, 5078000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(59, 'Brisbane', 69, 2487000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(60, 'Perth', 69, 2059000, 0, '2025-04-25 01:07:00', '2025-04-25 01:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int UNSIGNED NOT NULL,
  `adress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `num_passeport` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `adress`, `pays`, `num_passeport`, `date_naissance`) VALUES
(2, '123 Cheraga', 'Algérie', 'DZ123456', '2004-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` int UNSIGNED NOT NULL,
  `date_generation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `montant_ht` decimal(12,2) NOT NULL,
  `tva` decimal(12,2) NOT NULL,
  `montant_total` decimal(12,2) NOT NULL,
  `est_payee` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_fact_reservation` (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestionnaire`
--

DROP TABLE IF EXISTS `gestionnaire`;
CREATE TABLE IF NOT EXISTS `gestionnaire` (
  `id` int UNSIGNED NOT NULL,
  `poste` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departement` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestionnaire`
--

INSERT INTO `gestionnaire` (`id`, `poste`, `departement`) VALUES
(3, 'Responsable Réservations', 'Service Client');

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

DROP TABLE IF EXISTS `hotel`;
CREATE TABLE IF NOT EXISTS `hotel` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ville` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etoiles` tinyint DEFAULT NULL,
  `chambres_disponible` int NOT NULL,
  `prix_par_nuit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nom_hotel` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel`
--

INSERT INTO `hotel` (`id`, `nom`, `adress`, `ville`, `pays`, `etoiles`, `chambres_disponible`, `prix_par_nuit`, `image`) VALUES
(26, 'El Aurassi', 'Rue Hassiba Ben Bouali, Hydra', 'Alger', 'Algérie', 5, 15, 24300.00, 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Hotel-El-Aurassi.jpg'),
(27, 'Sofitel Algiers Hamma Garden', 'Les Jardins de l’Hamma', 'Alger', 'Algérie', 5, 25, 29700.00, 'https://www.sofitel-algiers.com/wp-content/uploads/2016/09/sofitel-algiers-hotel-garden.jpg'),
(28, 'Sheraton Club des Pins Resort', 'BP 12, Club des Pins', 'Alger', 'Algérie', 5, 20, 27000.00, 'https://upload.wikimedia.org/wikipedia/commons/6/64/Sheraton_Club_Des_Pins_Resort.jpg'),
(29, 'Royal Hotel Oran', 'Avenue de l’ALN', 'Oran', 'Algérie', 4, 10, 14800.00, 'https://upload.wikimedia.org/wikipedia/commons/f/fd/Royal_Hotel_Oran.jpg'),
(30, 'Novotel Constantine', 'Parc des Rocades, El Khroub', 'Constantine', 'Algérie', 4, 8, 12800.00, 'https://upload.wikimedia.org/wikipedia/commons/4/4e/Novotel_Constantine.jpg'),
(31, 'The Residence Tunis', 'Corniche', 'Tunis', 'Tunisie', 5, 30, 27000.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1a/cf/50/62/the-residence-tunis.jpg'),
(32, 'Hotel Carlton', 'Avenue Mohamed V', 'Tunis', 'Tunisie', 4, 12, 16200.00, 'https://media-cdn.tripadvisor.com/media/photo-s/0e/c1/90/ed/carlton-hotel-tunis.jpg'),
(33, 'La Mamounia', 'Avenue Bab Jdid', 'Marrakech', 'Maroc', 5, 25, 40500.00, 'https://media-cdn.tripadvisor.com/media/photo-s/0e/5d/d6/7e/royal-mansour-marrakech.jpg'),
(34, 'Royal Mansour Marrakech', 'Rue Abou Abbas El Sebti', 'Marrakech', 'Maroc', 5, 15, 60750.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1b/75/82/68/four-seasons-resort-marrakech.jpg'),
(35, 'Four Seasons Resort Marrakech', 'Km 5, Route de l’Oukaïmeden', 'Marrakech', 'Maroc', 5, 12, 47250.00, 'https://upload.wikimedia.org/wikipedia/commons/0/02/Hyatt_Regency_Casablanca.jpg'),
(36, 'Hyatt Regency Casablanca', 'Place des Nations Unies', 'Casablanca', 'Maroc', 5, 30, 24300.00, 'https://upload.wikimedia.org/wikipedia/commons/0/02/Hyatt_Regency_Casablanca.jpg'),
(37, 'Ritz Paris', '15 Place Vendôme', 'Paris', 'France', 5, 20, 74300.00, 'https://upload.wikimedia.org/wikipedia/commons/e/e6/Ritz_Paris.jpg'),
(38, 'Hôtel Plaza Athénée', '25 Avenue Montaigne', 'Paris', 'France', 5, 10, 70200.00, 'https://media-cdn.tripadvisor.com/media/photo-s/0f/c1/94/1e/plaza-athenee.jpg'),
(39, 'The Savoy', 'Strand', 'Londres', 'Royaume-Uni', 5, 15, 64800.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1b/c9/77/16/the-savoy.jpg'),
(40, 'The Ritz London', '150 Piccadilly', 'Londres', 'Royaume-Uni', 5, 8, 81000.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1b/8e/90/21/the-ritz-london.jpg'),
(41, 'The Plaza', '768 5th Ave', 'New York', 'États-Unis', 5, 30, 87750.00, 'https://media-cdn.tripadvisor.com/media/photo-s/0e/ed/7f/3f/the-plaza.jpg'),
(42, 'Four Seasons Hotel New York', '57 E 57th St', 'New York', 'États-Unis', 5, 20, 94500.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1b/25/0b/07/four-seasons-hotel-new-york.jpg'),
(43, 'Burj Al Arab', 'Jumeirah St', 'Dubai', 'Émirats Arabes Unis', 5, 10, 135000.00, 'https://upload.wikimedia.org/wikipedia/commons/6/63/Burj_Al_Arab_from_ground.jpg'),
(44, 'Atlantis The Palm', 'Crescent Rd, The Palm Jumeirah', 'Dubai', 'Émirats Arabes Unis', 5, 20, 60750.00, 'https://upload.wikimedia.org/wikipedia/commons/e/e2/Atlantis_the_palm.jpg'),
(45, 'Steigenberger Airport Hotel', 'Unterschweinstiege 16', 'Francfort', 'Allemagne', 4, 50, 21600.00, 'https://upload.wikimedia.org/wikipedia/commons/4/4b/Steigenberger_Airport_Hotel_Frankfurt.jpg'),
(46, 'Villa Kennedy, a Rocco Forte Hotel', 'Kennedyallee 70', 'Francfort', 'Allemagne', 5, 10, 36450.00, 'https://upload.wikimedia.org/wikipedia/commons/0/09/Villa_Kennedy_Frankfurt.jpg'),
(47, 'Çırağan Palace Kempinski', 'Çırağan Caddesi', 'Istanbul', 'Turquie', 5, 5, 45900.00, 'https://upload.wikimedia.org/wikipedia/commons/a/a4/%C3%87%C4%B1ra%C4%9Fan_Palace_Kempinski_2019.jpg'),
(48, 'Four Seasons Hotel Istanbul at Sultanahmet', 'Tevkifhane Sokak No.1', 'Istanbul', 'Turquie', 5, 12, 43200.00, 'https://upload.wikimedia.org/wikipedia/commons/e/e0/Four_Seasons_Istanbul_Sultanahmet_Exterior_2013.jpg'),
(49, 'Park Hyatt Tokyo', '3-7-1-2 Nishi Shinjuku', 'Tokyo', 'Japon', 5, 8, 81000.00, 'https://media-cdn.tripadvisor.com/media/photo-s/1a/66/19/7f/park-hyatt-tokyo.jpg'),
(50, 'Mandarin Oriental, Tokyo', '2-1-1 Nihonbashi Muromachi', 'Tokyo', 'Japon', 5, 10, 74250.00, 'https://media-cdn.tripadvisor.com/media/photo-s/0e/62/ff/d5/mandarin-oriental-tokyo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `methode_paiement`
--

DROP TABLE IF EXISTS `methode_paiement`;
CREATE TABLE IF NOT EXISTS `methode_paiement` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_methode` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int UNSIGNED NOT NULL,
  `gestionnaire_id` int UNSIGNED DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_envoi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `est_lue` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_notif_user` (`utilisateur_id`),
  KEY `fk_notif_gest` (`gestionnaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` int UNSIGNED NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `date_paiement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `methode_paiement_id` int UNSIGNED NOT NULL,
  `reference_transaction` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_paiement_res` (`reservation_id`),
  KEY `fk_paiement_met` (`methode_paiement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pays`
--

DROP TABLE IF EXISTS `pays`;
CREATE TABLE IF NOT EXISTS `pays` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `continent` varchar(50) NOT NULL,
  `code` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_pays_nom` (`nom`(250))
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pays`
--

INSERT INTO `pays` (`id`, `nom`, `continent`, `code`, `created_at`, `updated_at`) VALUES
(1, 'France', 'Europe', 'FR', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(2, 'Allemagne', 'Europe', 'DE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(3, 'Italie', 'Europe', 'IT', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(4, 'Espagne', 'Europe', 'ES', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(5, 'Portugal', 'Europe', 'PT', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(6, 'Royaume-Uni', 'Europe', 'GB', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(7, 'Pays-Bas', 'Europe', 'NL', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(8, 'Belgique', 'Europe', 'BE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(9, 'Suisse', 'Europe', 'CH', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(10, 'Autriche', 'Europe', 'AT', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(11, 'Grèce', 'Europe', 'GR', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(12, 'Suède', 'Europe', 'SE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(13, 'Norvège', 'Europe', 'NO', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(14, 'Danemark', 'Europe', 'DK', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(15, 'Finlande', 'Europe', 'FI', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(16, 'Pologne', 'Europe', 'PL', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(17, 'République Tchèque', 'Europe', 'CZ', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(18, 'Hongrie', 'Europe', 'HU', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(19, 'Roumanie', 'Europe', 'RO', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(20, 'Bulgarie', 'Europe', 'BG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(21, 'Maroc', 'Afrique', 'MA', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(22, 'Algérie', 'Afrique', 'DZ', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(23, 'Tunisie', 'Afrique', 'TN', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(24, 'Égypte', 'Afrique', 'EG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(25, 'Sénégal', 'Afrique', 'SN', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(26, 'Côte d\'Ivoire', 'Afrique', 'CI', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(27, 'Mali', 'Afrique', 'ML', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(28, 'Nigeria', 'Afrique', 'NG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(29, 'Afrique du Sud', 'Afrique', 'ZA', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(30, 'Kenya', 'Afrique', 'KE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(31, 'Éthiopie', 'Afrique', 'ET', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(32, 'Ghana', 'Afrique', 'GH', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(33, 'Cameroun', 'Afrique', 'CM', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(34, 'Tanzanie', 'Afrique', 'TZ', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(35, 'Ouganda', 'Afrique', 'UG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(36, 'États-Unis', 'Amérique du Nord', 'US', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(37, 'Canada', 'Amérique du Nord', 'CA', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(38, 'Mexique', 'Amérique du Nord', 'MX', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(39, 'Cuba', 'Amérique du Nord', 'CU', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(40, 'Haïti', 'Amérique du Nord', 'HT', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(41, 'République Dominicaine', 'Amérique du Nord', 'DO', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(42, 'Brésil', 'Amérique du Sud', 'BR', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(43, 'Argentine', 'Amérique du Sud', 'AR', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(44, 'Chili', 'Amérique du Sud', 'CL', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(45, 'Colombie', 'Amérique du Sud', 'CO', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(46, 'Pérou', 'Amérique du Sud', 'PE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(47, 'Venezuela', 'Amérique du Sud', 'VE', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(48, 'Équateur', 'Amérique du Sud', 'EC', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(49, 'Bolivie', 'Amérique du Sud', 'BO', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(50, 'Paraguay', 'Amérique du Sud', 'PY', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(51, 'Uruguay', 'Amérique du Sud', 'UY', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(52, 'Chine', 'Asie', 'CN', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(53, 'Japon', 'Asie', 'JP', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(54, 'Corée du Sud', 'Asie', 'KR', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(55, 'Inde', 'Asie', 'IN', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(56, 'Indonésie', 'Asie', 'ID', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(57, 'Thaïlande', 'Asie', 'TH', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(58, 'Vietnam', 'Asie', 'VN', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(59, 'Malaisie', 'Asie', 'MY', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(60, 'Singapour', 'Asie', 'SG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(61, 'Philippines', 'Asie', 'PH', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(62, 'Pakistan', 'Asie', 'PK', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(63, 'Bangladesh', 'Asie', 'BD', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(64, 'Sri Lanka', 'Asie', 'LK', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(65, 'Népal', 'Asie', 'NP', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(66, 'Cambodge', 'Asie', 'KH', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(67, 'Laos', 'Asie', 'LA', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(68, 'Myanmar', 'Asie', 'MM', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(69, 'Australie', 'Océanie', 'AU', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(70, 'Nouvelle-Zélande', 'Océanie', 'NZ', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(71, 'Fidji', 'Océanie', 'FJ', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(72, 'Papouasie-Nouvelle-Guinée', 'Océanie', 'PG', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(73, 'Samoa', 'Océanie', 'WS', '2025-04-25 01:07:00', '2025-04-25 01:07:00'),
(74, 'Turquie', 'Asie', 'TR', '2025-05-04 16:27:37', '2025-05-04 16:27:37'),
(75, 'Émirats Arabes Unis', 'Asie', 'AE', '2025-05-04 16:27:37', '2025-05-04 16:27:37'),
(77, 'Russie', 'Europe', 'RU', '2025-05-04 16:27:37', '2025-05-04 16:27:37'),
(78, 'Irlande', 'Europe', 'IE', '2025-05-04 16:27:37', '2025-05-04 16:27:37');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_vol` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom_hotel` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_chambres` int NOT NULL DEFAULT '1',
  `type_chambre` enum('simple','double','suite','familiale') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_id` int UNSIGNED NOT NULL,
  `date_reservation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `montant_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `est_paye` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_res_client` (`client_id`),
  KEY `fk_res_statut` (`statut`),
  KEY `fk_reservation_vol` (`numero_vol`),
  KEY `fk_reservation_nom_hotel` (`nom_hotel`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `numero_vol`, `nom_hotel`, `nombre_chambres`, `type_chambre`, `client_id`, `date_reservation`, `statut`, `montant_total`, `est_paye`) VALUES
(5, 'DZ6301', 'Ritz Paris', 1, '', 1, '2025-05-19 19:44:52', '0', 166055.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `nom`) VALUES
(3, 'admin'),
(1, 'client'),
(2, 'gestionnaire');

-- --------------------------------------------------------

--
-- Table structure for table `statue_reservation`
--

DROP TABLE IF EXISTS `statue_reservation`;
CREATE TABLE IF NOT EXISTS `statue_reservation` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_statut` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

DROP TABLE IF EXISTS `transport`;
CREATE TABLE IF NOT EXISTS `transport` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` int UNSIGNED NOT NULL,
  `lieu_depart` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lieu_arrivee` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_heure_depart` datetime DEFAULT NULL,
  `date_heure_arrivee` datetime DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `places_disponibles` int UNSIGNED NOT NULL DEFAULT '0',
  `numero_transport` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_transport_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tr_reservation` (`reservation_id`),
  KEY `fk_tr_type` (`type_transport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type_transport`
--

DROP TABLE IF EXISTS `type_transport`;
CREATE TABLE IF NOT EXISTS `type_transport` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_typetr` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role_id` int UNSIGNED NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `fk_utilisateur_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `password`, `telephone`, `date_creation`, `role_id`, `role_name`) VALUES
(1, 'Kameli', 'Moncef', 'moncef@gmail.com', '3', '0000000003', '2025-05-04 15:22:40', 3, 'admin'),
(2, 'Chibah', 'Adel', 'adel@gmail.com', '1', '0000000001', '2025-05-04 15:22:40', 1, 'client'),
(3, 'Ferhaoui', 'Khaled', 'khaled@gmail.com', '2', '0000000003', '2025-05-04 15:22:40', 2, 'gestionnaire');

--
-- Triggers `utilisateur`
--
DROP TRIGGER IF EXISTS `after_utilisateur_insert`;
DELIMITER $$
CREATE TRIGGER `after_utilisateur_insert` AFTER INSERT ON `utilisateur` FOR EACH ROW BEGIN
    IF NEW.role_id = 1 THEN
        INSERT INTO client (id) VALUES (NEW.id);
    ELSEIF NEW.role_id = 2 THEN
        INSERT INTO gestionnaire (id) VALUES (NEW.id);
    ELSEIF NEW.role_id = 3 THEN
        INSERT INTO admin (id, niveau) VALUES (NEW.id, 1); -- set niveau as you wish
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vol`
--

DROP TABLE IF EXISTS `vol`;
CREATE TABLE IF NOT EXISTS `vol` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_vol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `compagnie_aerienne` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_depart` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_arrivee` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `continent` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `type_vol` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `places_disponibles` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_vol` (`numero_vol`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vol`
--

INSERT INTO `vol` (`id`, `numero_vol`, `compagnie_aerienne`, `aeroport_depart`, `aeroport_arrivee`, `destination`, `continent`, `date_depart`, `date_arrivee`, `type_vol`, `prix`, `places_disponibles`) VALUES
(151, 'DZ1768', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-14 10:39:00', '2025-06-08 06:13:00', 'Aller-retour', 40248.00, 97),
(152, 'DZ4625', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-08-08 07:23:00', '2025-06-08 19:03:00', 'Aller simple', 11770.00, 195),
(153, 'DZ7164', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Kuala Lumpur KUL (Malaisie)', 'Malaisie', 'Asie', '2025-08-19 09:27:00', '2025-06-10 01:02:00', 'Aller-retour', 73841.00, 46),
(154, 'DZ9892', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-06-17 22:05:00', '2025-08-05 23:56:00', 'Aller-retour', 64421.00, 123),
(155, 'DZ1698', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-07-31 09:54:00', '2025-07-12 10:51:00', 'Aller-retour', 53553.00, 63),
(156, 'DZ2145', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-07-05 17:35:00', '2025-08-08 19:57:00', 'Aller-retour', 85736.00, 110),
(157, 'DZ9829', 'Singapore Airlines', 'Aéroport d’Alger - Houari Boumediene', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-08-05 05:04:00', '2025-08-03 23:53:00', 'Aller simple', 77017.00, 73),
(158, 'DZ5303', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-07-01 10:15:00', '2025-09-04 00:12:00', 'Aller simple', 29081.00, 141),
(159, 'DZ8330', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-07-24 05:19:00', '2025-09-05 22:42:00', 'Aller-retour', 39379.00, 165),
(160, 'DZ9881', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Lagos LOS (Nigeria)', 'Nigeria', 'Afrique', '2025-08-27 00:14:00', '2025-06-21 00:35:00', 'Aller-retour', 45640.00, 184),
(161, 'DZ2632', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-08-30 21:26:00', '2025-07-26 13:00:00', 'Aller-retour', 53528.00, 225),
(162, 'DZ8288', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Afrique', '2025-06-21 01:37:00', '2025-08-30 20:24:00', 'Aller simple', 13489.00, 90),
(164, 'DZ5484', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-08-12 11:59:00', '2025-07-25 17:58:00', 'Aller-retour', 76149.00, 113),
(165, 'DZ4769', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-08-27 16:33:00', '2025-08-12 04:31:00', 'Aller simple', 47934.00, 206),
(166, 'DZ6301', 'Lufthansa', 'Aéroport de Béjaïa - Soummam', 'Paris CDG (France)', 'France', 'Europe', '2025-06-30 18:56:00', '2025-07-07 00:04:00', 'Aller simple', 89255.00, 129),
(167, 'DZ3606', 'Air Algérie', 'Aéroport d’Annaba - Rabah Bitat', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-08-06 00:49:00', '2025-06-17 05:38:00', 'Aller-retour', 61422.00, 168),
(168, 'DZ2977', 'Turkish Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Asie', '2025-06-23 20:38:00', '2025-08-03 03:51:00', 'Aller-retour', 16927.00, 80),
(169, 'DZ9068', 'Etihad Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-06-02 20:12:00', '2025-07-22 18:27:00', 'Aller simple', 44440.00, 127),
(170, 'DZ9309', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Barcelone BCN (Espagne)', 'Espagne', 'Europe', '2025-07-11 01:01:00', '2025-09-01 14:07:00', 'Aller simple', 63932.00, 65),
(171, 'DZ8020', 'Emirates', 'Aéroport de Tamanrasset - Aguenar', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-08-28 22:46:00', '2025-06-11 21:19:00', 'Aller-retour', 49145.00, 206),
(172, 'DZ5855', 'Turkish Airlines', 'Aéroport de Béjaïa - Soummam', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-06-09 18:32:00', '2025-07-19 14:45:00', 'Aller simple', 29946.00, 184),
(173, 'DZ7778', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Asie', '2025-07-05 10:00:00', '2025-08-19 21:07:00', 'Aller simple', 36774.00, 101),
(174, 'DZ6688', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Berlin BER (Allemagne)', 'Allemagne', 'Europe', '2025-08-21 17:53:00', '2025-07-03 00:00:00', 'Aller simple', 77820.00, 171),
(175, 'DZ7130', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-07-26 13:05:00', '2025-08-09 08:39:00', 'Aller simple', 52645.00, 157),
(176, 'DZ3641', 'Etihad Airways', 'Aéroport de Tamanrasset - Aguenar', 'Cairo CAI (Égypte)', 'Égypte', 'Afrique', '2025-08-01 21:25:00', '2025-07-12 22:22:00', 'Aller simple', 46646.00, 234),
(177, 'DZ3510', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Asie', '2025-06-02 12:36:00', '2025-07-16 06:09:00', 'Aller simple', 27151.00, 131),
(178, 'DZ7014', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-08-31 05:03:00', '2025-08-06 07:25:00', 'Aller-retour', 63914.00, 193),
(179, 'DZ7348', 'Turkish Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-08-17 00:36:00', '2025-09-07 03:40:00', 'Aller-retour', 54511.00, 45),
(180, 'DZ7209', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-06-23 00:22:00', '2025-08-21 23:20:00', 'Aller simple', 23581.00, 128),
(181, 'DZ7662', 'Qatar Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-08-15 11:59:00', '2025-07-18 10:43:00', 'Aller-retour', 76827.00, 125),
(182, 'DZ7016', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-08-25 05:21:00', '2025-06-17 10:49:00', 'Aller-retour', 51406.00, 58),
(184, 'DZ3333', 'Qatar Airways', 'Aéroport de Béjaïa - Soummam', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-07-11 08:20:00', '2025-08-13 15:56:00', 'Aller simple', 68475.00, 240),
(185, 'DZ6391', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Zurich ZRH (Suisse)', 'Suisse', 'Europe', '2025-07-07 08:39:00', '2025-06-20 21:01:00', 'Aller-retour', 65233.00, 71),
(186, 'DZ8747', 'Etihad Airways', 'Aéroport d’Alger - Houari Boumediene', 'Barcelone BCN (Espagne)', 'Espagne', 'Europe', '2025-07-04 22:32:00', '2025-08-27 22:02:00', 'Aller-retour', 37704.00, 176),
(187, 'DZ3525', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-06-24 14:12:00', '2025-06-29 17:54:00', 'Aller-retour', 58636.00, 90),
(188, 'DZ5891', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Casablanca CMN (Maroc)', 'Maroc', 'Afrique', '2025-08-27 17:49:00', '2025-09-01 06:29:00', 'Aller-retour', 17352.00, 33),
(189, 'DZ8282', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-01 07:37:00', '2025-06-17 21:58:00', 'Aller simple', 25605.00, 131),
(190, 'DZ7520', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-07-03 17:39:00', '2025-06-07 04:48:00', 'Aller simple', 16958.00, 44),
(191, 'DZ1603', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-07-21 12:57:00', '2025-07-01 10:25:00', 'Aller-retour', 48467.00, 193),
(192, 'DZ3477', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Asie', '2025-06-19 22:03:00', '2025-07-19 09:26:00', 'Aller-retour', 43625.00, 40),
(193, 'DZ9863', 'Etihad Airways', 'Aéroport de Béjaïa - Soummam', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-08-23 05:27:00', '2025-07-24 11:43:00', 'Aller-retour', 31932.00, 196),
(194, 'DZ9578', 'Lufthansa', 'Aéroport d’Annaba - Rabah Bitat', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-06-01 03:38:00', '2025-08-17 02:08:00', 'Aller simple', 33576.00, 31),
(195, 'DZ2258', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-06-24 18:10:00', '2025-07-21 22:18:00', 'Aller simple', 55404.00, 237),
(196, 'DZ1101', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'Stockholm ARN (Suède)', 'Suède', 'Europe', '2025-06-05 11:15:00', '2025-08-17 08:14:00', 'Aller simple', 11961.00, 209),
(197, 'DZ1139', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-08-22 15:32:00', '2025-08-19 08:22:00', 'Aller simple', 72964.00, 194),
(198, 'DZ4370', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-07-02 00:05:00', '2025-07-09 14:51:00', 'Aller-retour', 74562.00, 136),
(199, 'DZ1023', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-10 02:13:00', '2025-08-15 08:26:00', 'Aller simple', 69614.00, 47),
(200, 'DZ2398', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Tel Aviv TLV (Israël)', 'Israël', 'Asie', '2025-07-24 01:38:00', '2025-09-01 21:38:00', 'Aller-retour', 41029.00, 74),
(201, 'DZ8555', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-07-02 04:53:00', '2025-08-30 22:54:00', 'Aller-retour', 88190.00, 125),
(202, 'DZ3044', 'Singapore Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Asie', '2025-08-20 22:02:00', '2025-07-10 21:12:00', 'Aller simple', 33730.00, 166),
(203, 'DZ2874', 'Tassili Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-24 12:45:00', '2025-06-29 04:08:00', 'Aller-retour', 53158.00, 215),
(204, 'DZ6290', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Asie', '2025-07-06 22:27:00', '2025-07-23 02:03:00', 'Aller-retour', 28837.00, 98),
(205, 'DZ8695', 'Qatar Airways', 'Aéroport d’Alger - Houari Boumediene', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-08-11 17:15:00', '2025-06-19 23:30:00', 'Aller simple', 56260.00, 61),
(206, 'DZ9901', 'Lufthansa', 'Aéroport d’Annaba - Rabah Bitat', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-06-10 17:19:00', '2025-07-17 08:18:00', 'Aller-retour', 25679.00, 114),
(207, 'DZ4054', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-06-28 16:29:00', '2025-07-15 15:56:00', 'Aller-retour', 51395.00, 189),
(208, 'DZ1595', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-07-11 19:42:00', '2025-06-15 12:11:00', 'Aller simple', 50376.00, 149),
(209, 'DZ2908', 'Emirates', 'Aéroport de Constantine - Mohamed Boudiaf', 'Madrid MAD (Espagne)', 'Espagne', 'Europe', '2025-06-23 04:06:00', '2025-06-11 00:54:00', 'Aller-retour', 70988.00, 228),
(210, 'DZ2975', 'Qatar Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Buenos Aires EZE (Argentine)', 'Argentine', 'Amérique du Sud', '2025-07-14 14:39:00', '2025-07-15 03:25:00', 'Aller simple', 61720.00, 58),
(211, 'DZ7390', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-07-01 11:26:00', '2025-08-11 07:19:00', 'Aller-retour', 63258.00, 176),
(212, 'DZ3971', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Berlin BER (Allemagne)', 'Allemagne', 'Europe', '2025-07-16 09:24:00', '2025-08-30 10:22:00', 'Aller-retour', 76018.00, 32),
(213, 'DZ6257', 'Singapore Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-07-05 08:45:00', '2025-07-30 22:46:00', 'Aller-retour', 45179.00, 129),
(214, 'DZ9455', 'Qatar Airways', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-06-15 07:01:00', '2025-06-29 03:03:00', 'Aller-retour', 77392.00, 174),
(215, 'DZ7850', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-07-28 12:40:00', '2025-08-21 05:36:00', 'Aller simple', 13189.00, 51),
(216, 'DZ4354', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-06-14 14:33:00', '2025-06-10 10:01:00', 'Aller simple', 79881.00, 132),
(217, 'DZ7431', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Asie', '2025-08-02 20:15:00', '2025-08-09 18:46:00', 'Aller simple', 70439.00, 249),
(218, 'DZ7521', 'EgyptAir', 'Aéroport de Béjaïa - Soummam', 'Bucharest OTP (Roumanie)', 'Roumanie', 'Europe', '2025-07-16 20:44:00', '2025-06-16 13:20:00', 'Aller simple', 41208.00, 178),
(219, 'DZ2863', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-07-27 12:46:00', '2025-07-03 05:15:00', 'Aller simple', 64250.00, 64),
(220, 'DZ7788', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-27 03:56:00', '2025-06-24 08:03:00', 'Aller simple', 52241.00, 134),
(221, 'DZ8143', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Paris CDG (France)', 'France', 'Europe', '2025-07-15 02:03:00', '2025-08-30 17:38:00', 'Aller simple', 86433.00, 133),
(222, 'DZ5442', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-27 23:58:00', '2025-06-03 03:42:00', 'Aller simple', 67897.00, 47),
(224, 'DZ7570', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Afrique', '2025-08-18 15:39:00', '2025-07-12 21:18:00', 'Aller-retour', 14157.00, 165),
(225, 'DZ9235', 'Etihad Airways', 'Aéroport de Béjaïa - Soummam', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-08-16 13:20:00', '2025-06-06 02:31:00', 'Aller-retour', 86788.00, 82),
(226, 'DZ3751', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-06-21 15:34:00', '2025-08-24 20:30:00', 'Aller-retour', 83863.00, 207),
(227, 'DZ3428', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-07-21 10:33:00', '2025-07-24 20:41:00', 'Aller-retour', 20156.00, 223),
(228, 'DZ1181', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-07-15 05:41:00', '2025-08-19 21:04:00', 'Aller simple', 52967.00, 178),
(229, 'DZ7973', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-08-12 05:42:00', '2025-08-30 11:37:00', 'Aller-retour', 71650.00, 63),
(230, 'DZ4990', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-08-31 02:36:00', '2025-08-07 11:29:00', 'Aller simple', 71918.00, 223),
(231, 'DZ1655', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-08-02 13:41:00', '2025-08-23 03:13:00', 'Aller-retour', 74405.00, 181),
(232, 'DZ1343', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-08-01 02:25:00', '2025-08-24 00:33:00', 'Aller-retour', 13208.00, 187),
(233, 'DZ5201', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Paris CDG (France)', 'France', 'Europe', '2025-07-07 13:36:00', '2025-07-06 21:25:00', 'Aller simple', 81814.00, 97),
(234, 'DZ8514', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-07-23 15:29:00', '2025-07-22 02:57:00', 'Aller-retour', 62423.00, 113),
(235, 'DZ9359', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-08-04 21:20:00', '2025-06-10 04:50:00', 'Aller-retour', 57086.00, 210),
(236, 'DZ3948', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-06-02 02:27:00', '2025-06-02 15:12:00', 'Aller simple', 67557.00, 145),
(238, 'DZ3933', 'Emirates', 'Aéroport d’Alger - Houari Boumediene', 'Moscou SVO (Russie)', 'Russie', 'Europe', '2025-08-18 04:25:00', '2025-07-29 13:05:00', 'Aller-retour', 62656.00, 140),
(239, 'DZ5859', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-06-25 18:09:00', '2025-07-08 11:19:00', 'Aller-retour', 35293.00, 34),
(240, 'DZ2373', 'Etihad Airways', 'Aéroport d’Alger - Houari Boumediene', 'Madrid MAD (Espagne)', 'Espagne', 'Europe', '2025-08-14 02:00:00', '2025-08-13 16:09:00', 'Aller simple', 26327.00, 62),
(242, 'DZ4843', 'Tassili Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-19 16:48:00', '2025-09-05 11:28:00', 'Aller simple', 88468.00, 201),
(243, 'DZ9573', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-08-05 19:53:00', '2025-06-07 09:58:00', 'Aller simple', 56872.00, 34),
(244, 'DZ4189', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Moscou SVO (Russie)', 'Russie', 'Europe', '2025-06-17 05:41:00', '2025-08-14 16:09:00', 'Aller simple', 29633.00, 32),
(245, 'DZ3835', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-27 22:47:00', '2025-06-19 11:56:00', 'Aller simple', 57200.00, 144),
(246, 'DZ8391', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-07-17 23:18:00', '2025-08-02 07:39:00', 'Aller-retour', 20887.00, 212),
(247, 'DZ7571', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-25 16:38:00', '2025-06-22 23:19:00', 'Aller simple', 15695.00, 51),
(248, 'DZ3413', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-08-02 18:49:00', '2025-08-19 11:05:00', 'Aller-retour', 11085.00, 98),
(249, 'DZ5705', 'EgyptAir', 'Aéroport de Tamanrasset - Aguenar', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-17 08:15:00', '2025-06-28 10:22:00', 'Aller-retour', 11539.00, 66),
(250, 'DZ8030', 'Emirates', 'Aéroport de Tamanrasset - Aguenar', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-27 17:49:00', '2025-08-24 00:28:00', 'Aller-retour', 28676.00, 205),
(251, 'DZ3628', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-06-30 22:39:00', '2025-07-17 12:06:00', 'Aller-retour', 64733.00, 68),
(252, 'DZ8411', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-07-13 15:45:00', '2025-08-25 05:23:00', 'Aller-retour', 51783.00, 48),
(253, 'DZ8629', 'American Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Asie', '2025-06-29 06:22:00', '2025-06-16 08:23:00', 'Aller-retour', 73113.00, 181),
(254, 'DZ1710', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Paris CDG (France)', 'France', 'Europe', '2025-07-01 00:09:00', '2025-08-10 23:45:00', 'Aller simple', 11036.00, 179),
(255, 'DZ4162', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-07-18 03:07:00', '2025-06-27 20:36:00', 'Aller simple', 46819.00, 235),
(256, 'DZ3515', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-06-28 11:25:00', '2025-08-12 11:10:00', 'Aller-retour', 64007.00, 162),
(257, 'DZ9949', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Tel Aviv TLV (Israël)', 'Israël', 'Afrique', '2025-06-11 19:44:00', '2025-07-04 04:05:00', 'Aller simple', 57322.00, 65),
(258, 'DZ1291', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-08-27 00:12:00', '2025-09-04 08:42:00', 'Aller-retour', 50871.00, 172),
(260, 'DZ1469', 'Turkish Airlines', 'Aéroport de Béjaïa - Soummam', 'Amsterdam AMS (Pays-Bas)', 'Pays-Bas', 'Europe', '2025-06-11 09:33:00', '2025-08-06 14:07:00', 'Aller simple', 29003.00, 104),
(261, 'DZ9730', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Bucharest OTP (Roumanie)', 'Roumanie', 'Europe', '2025-07-07 10:56:00', '2025-07-11 07:12:00', 'Aller simple', 68015.00, 221),
(262, 'DZ2566', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-31 03:47:00', '2025-07-18 23:30:00', 'Aller simple', 79469.00, 119),
(263, 'DZ4894', 'American Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Los Angeles LAX (USA)', 'États-Unis', 'Amérique du Nord', '2025-07-13 21:00:00', '2025-07-07 21:22:00', 'Aller-retour', 31539.00, 196),
(264, 'DZ9786', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-07-23 02:43:00', '2025-07-09 17:31:00', 'Aller simple', 66674.00, 123),
(265, 'DZ9928', 'EgyptAir', 'Aéroport de Constantine - Mohamed Boudiaf', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Afrique', '2025-07-05 17:32:00', '2025-07-17 18:30:00', 'Aller simple', 73770.00, 147),
(266, 'DZ3602', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-06-14 07:03:00', '2025-07-11 17:35:00', 'Aller-retour', 56377.00, 181),
(267, 'DZ7360', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Zurich ZRH (Suisse)', 'Suisse', 'Europe', '2025-06-21 04:21:00', '2025-06-20 20:45:00', 'Aller-retour', 74893.00, 36),
(268, 'DZ7355', 'Emirates', 'Aéroport d’Alger - Houari Boumediene', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-07-22 05:26:00', '2025-07-21 08:14:00', 'Aller-retour', 82487.00, 243),
(269, 'DZ2270', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-08-19 03:04:00', '2025-09-03 14:07:00', 'Aller-retour', 80788.00, 127),
(270, 'DZ6023', 'Emirates', 'Aéroport d’Annaba - Rabah Bitat', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-06-29 02:34:00', '2025-07-23 01:39:00', 'Aller-retour', 40402.00, 92),
(271, 'DZ3579', 'Lufthansa', 'Aéroport d’Alger - Houari Boumediene', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-06-26 16:41:00', '2025-07-06 14:05:00', 'Aller-retour', 22333.00, 43),
(272, 'DZ8625', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-06-24 14:14:00', '2025-07-08 03:35:00', 'Aller-retour', 70953.00, 74),
(273, 'DZ7610', 'Air Algérie', 'Aéroport d’Alger - Houari Boumediene', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-06-28 18:57:00', '2025-07-20 11:03:00', 'Aller-retour', 41028.00, 30),
(274, 'DZ8669', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-24 16:43:00', '2025-07-20 08:18:00', 'Aller simple', 69657.00, 57),
(275, 'DZ4408', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Stockholm ARN (Suède)', 'Suède', 'Europe', '2025-07-08 15:03:00', '2025-07-05 13:46:00', 'Aller-retour', 14910.00, 199),
(276, 'DZ7071', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-06-30 17:45:00', '2025-07-29 14:17:00', 'Aller simple', 39228.00, 49),
(277, 'DZ4139', 'Emirates', 'Aéroport de Constantine - Mohamed Boudiaf', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-08-04 14:58:00', '2025-06-02 02:28:00', 'Aller simple', 20321.00, 113),
(278, 'DZ5560', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'New York JFK (USA)', 'États-Unis', 'Amérique du Nord', '2025-06-15 20:45:00', '2025-06-27 00:15:00', 'Aller-retour', 52403.00, 162),
(279, 'DZ4775', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-08 15:03:00', '2025-06-28 05:24:00', 'Aller simple', 11000.00, 152),
(280, 'DZ7738', 'Air Algérie', 'Aéroport d’Alger - Houari Boumediene', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-08-28 14:04:00', '2025-07-29 17:56:00', 'Aller-retour', 47078.00, 155),
(281, 'DZ5222', 'EgyptAir', 'Aéroport de Tamanrasset - Aguenar', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-08-24 23:05:00', '2025-07-28 11:40:00', 'Aller-retour', 13945.00, 226),
(282, 'DZ3803', 'Singapore Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Amsterdam AMS (Pays-Bas)', 'Pays-Bas', 'Europe', '2025-08-22 19:27:00', '2025-08-18 18:17:00', 'Aller simple', 79534.00, 46),
(283, 'DZ7959', 'EgyptAir', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-07-16 19:34:00', '2025-07-14 07:21:00', 'Aller simple', 76041.00, 239),
(284, 'DZ3495', 'Lufthansa', 'Aéroport de Béjaïa - Soummam', 'Casablanca CMN (Maroc)', 'Maroc', 'Afrique', '2025-07-08 17:24:00', '2025-08-23 00:33:00', 'Aller simple', 35295.00, 77),
(285, 'DZ2230', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-06-27 06:33:00', '2025-09-04 03:49:00', 'Aller simple', 38677.00, 135),
(286, 'DZ3916', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-07-16 04:22:00', '2025-07-08 16:18:00', 'Aller-retour', 48058.00, 158),
(287, 'DZ5528', 'Etihad Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Londres LHR (R.-U.)', 'R.-U.', 'Europe', '2025-07-16 03:12:00', '2025-08-03 11:27:00', 'Aller simple', 69501.00, 200),
(288, 'DZ6876', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-25 17:52:00', '2025-06-20 04:27:00', 'Aller simple', 61823.00, 203),
(289, 'DZ9964', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-07-02 04:57:00', '2025-06-25 04:13:00', 'Aller simple', 62169.00, 119),
(290, 'DZ1789', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-08-04 10:06:00', '2025-06-28 19:26:00', 'Aller simple', 26787.00, 80),
(291, 'DZ5680', 'American Airlines', 'Aéroport de Béjaïa - Soummam', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-06-21 13:09:00', '2025-06-14 22:31:00', 'Aller simple', 44296.00, 196),
(292, 'DZ5525', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-06-22 01:32:00', '2025-07-27 04:14:00', 'Aller-retour', 38830.00, 195),
(293, 'DZ7041', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-07-31 06:18:00', '2025-08-15 00:40:00', 'Aller-retour', 51659.00, 132),
(294, 'DZ7936', 'Emirates', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-06-03 08:40:00', '2025-07-04 13:50:00', 'Aller simple', 32549.00, 220),
(295, 'DZ5262', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-07-26 12:42:00', '2025-09-03 21:24:00', 'Aller-retour', 15426.00, 126),
(296, 'DZ9887', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-28 12:41:00', '2025-08-25 11:43:00', 'Aller simple', 11034.00, 167),
(297, 'DZ1741', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-06-09 01:05:00', '2025-06-26 18:15:00', 'Aller simple', 25145.00, 230),
(298, 'DZ9958', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Asie', '2025-06-10 11:04:00', '2025-08-29 08:57:00', 'Aller-retour', 44966.00, 107),
(299, 'DZ5107', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-20 22:59:00', '2025-06-24 22:07:00', 'Aller-retour', 86558.00, 43),
(300, 'DZ4856', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-05 22:38:00', '2025-07-10 22:29:00', 'Aller-retour', 78052.00, 95);

-- --------------------------------------------------------

--
-- Table structure for table `vol_backup`
--

DROP TABLE IF EXISTS `vol_backup`;
CREATE TABLE IF NOT EXISTS `vol_backup` (
  `id` int UNSIGNED NOT NULL DEFAULT '0',
  `numero_vol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `compagnie_aerienne` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_depart` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_arrivee` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `continent` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `type_vol` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `places_disponibles` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vol_backup`
--

INSERT INTO `vol_backup` (`id`, `numero_vol`, `compagnie_aerienne`, `aeroport_depart`, `aeroport_arrivee`, `destination`, `continent`, `date_depart`, `date_arrivee`, `type_vol`, `prix`, `places_disponibles`) VALUES
(151, 'DZ1768', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-14 10:39:00', '2025-06-08 06:13:00', 'Aller-retour', 40248.00, 97),
(152, 'DZ4625', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-08-08 07:23:00', '2025-06-08 19:03:00', 'Aller simple', 11770.00, 195),
(153, 'DZ7164', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Kuala Lumpur KUL (Malaisie)', 'Malaisie', 'Asie', '2025-08-19 09:27:00', '2025-06-10 01:02:00', 'Aller-retour', 73841.00, 46),
(154, 'DZ9892', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-06-17 22:05:00', '2025-08-05 23:56:00', 'Aller-retour', 64421.00, 123),
(155, 'DZ1698', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-07-31 09:54:00', '2025-07-12 10:51:00', 'Aller-retour', 53553.00, 63),
(156, 'DZ2145', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-07-05 17:35:00', '2025-08-08 19:57:00', 'Aller-retour', 85736.00, 110),
(157, 'DZ9829', 'Singapore Airlines', 'Aéroport d’Alger - Houari Boumediene', 'New York JFK (USA)', 'USA', 'Amérique du Nord', '2025-08-05 05:04:00', '2025-08-03 23:53:00', 'Aller simple', 77017.00, 73),
(158, 'DZ5303', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-07-01 10:15:00', '2025-09-04 00:12:00', 'Aller simple', 29081.00, 141),
(159, 'DZ8330', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-07-24 05:19:00', '2025-09-05 22:42:00', 'Aller-retour', 39379.00, 165),
(160, 'DZ9881', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Lagos LOS (Nigeria)', 'Nigeria', 'Afrique', '2025-08-27 00:14:00', '2025-06-21 00:35:00', 'Aller-retour', 45640.00, 184),
(161, 'DZ2632', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-08-30 21:26:00', '2025-07-26 13:00:00', 'Aller-retour', 53528.00, 225),
(162, 'DZ8288', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Asie', '2025-06-21 01:37:00', '2025-08-30 20:24:00', 'Aller simple', 13489.00, 90),
(163, 'DZ3144', 'Qatar Airways', 'Aéroport d’Alger - Houari Boumediene', 'Riyad RUH (Arabie Saoudite)', 'Arabie Saoudite', 'Amérique du Nord', '2025-07-13 00:41:00', '2025-07-12 20:05:00', 'Aller simple', 46122.00, 155),
(164, 'DZ5484', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-08-12 11:59:00', '2025-07-25 17:58:00', 'Aller-retour', 76149.00, 113),
(165, 'DZ4769', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-08-27 16:33:00', '2025-08-12 04:31:00', 'Aller simple', 47934.00, 206),
(166, 'DZ6301', 'Lufthansa', 'Aéroport de Béjaïa - Soummam', 'Paris CDG (France)', 'France', 'Europe', '2025-06-30 18:56:00', '2025-07-07 00:04:00', 'Aller simple', 89255.00, 129),
(167, 'DZ3606', 'Air Algérie', 'Aéroport d’Annaba - Rabah Bitat', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-08-06 00:49:00', '2025-06-17 05:38:00', 'Aller-retour', 61422.00, 168),
(168, 'DZ2977', 'Turkish Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Europe', '2025-06-23 20:38:00', '2025-08-03 03:51:00', 'Aller-retour', 16927.00, 80),
(169, 'DZ9068', 'Etihad Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-06-02 20:12:00', '2025-07-22 18:27:00', 'Aller simple', 44440.00, 127),
(170, 'DZ9309', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Barcelone BCN (Espagne)', 'Espagne', 'Europe', '2025-07-11 01:01:00', '2025-09-01 14:07:00', 'Aller simple', 63932.00, 65),
(171, 'DZ8020', 'Emirates', 'Aéroport de Tamanrasset - Aguenar', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-08-28 22:46:00', '2025-06-11 21:19:00', 'Aller-retour', 49145.00, 206),
(172, 'DZ5855', 'Turkish Airlines', 'Aéroport de Béjaïa - Soummam', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-06-09 18:32:00', '2025-07-19 14:45:00', 'Aller simple', 29946.00, 184),
(173, 'DZ7778', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Europe', '2025-07-05 10:00:00', '2025-08-19 21:07:00', 'Aller simple', 36774.00, 101),
(174, 'DZ6688', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Berlin BER (Allemagne)', 'Allemagne', 'Europe', '2025-08-21 17:53:00', '2025-07-03 00:00:00', 'Aller simple', 77820.00, 171),
(175, 'DZ7130', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'New York JFK (USA)', 'USA', 'Europe', '2025-07-26 13:05:00', '2025-08-09 08:39:00', 'Aller simple', 52645.00, 157),
(176, 'DZ3641', 'Etihad Airways', 'Aéroport de Tamanrasset - Aguenar', 'Cairo CAI (Égypte)', 'Égypte', 'Afrique', '2025-08-01 21:25:00', '2025-07-12 22:22:00', 'Aller simple', 46646.00, 234),
(177, 'DZ3510', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Europe', '2025-06-02 12:36:00', '2025-07-16 06:09:00', 'Aller simple', 27151.00, 131),
(178, 'DZ7014', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-08-31 05:03:00', '2025-08-06 07:25:00', 'Aller-retour', 63914.00, 193),
(179, 'DZ7348', 'Turkish Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-08-17 00:36:00', '2025-09-07 03:40:00', 'Aller-retour', 54511.00, 45),
(180, 'DZ7209', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-06-23 00:22:00', '2025-08-21 23:20:00', 'Aller simple', 23581.00, 128),
(181, 'DZ7662', 'Qatar Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-08-15 11:59:00', '2025-07-18 10:43:00', 'Aller-retour', 76827.00, 125),
(182, 'DZ7016', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-08-25 05:21:00', '2025-06-17 10:49:00', 'Aller-retour', 51406.00, 58),
(183, 'DZ1866', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'Riyad RUH (Arabie Saoudite)', 'Arabie Saoudite', 'Amérique du Sud', '2025-06-12 11:05:00', '2025-09-01 15:12:00', 'Aller simple', 64096.00, 174),
(184, 'DZ3333', 'Qatar Airways', 'Aéroport de Béjaïa - Soummam', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-07-11 08:20:00', '2025-08-13 15:56:00', 'Aller simple', 68475.00, 240),
(185, 'DZ6391', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Zurich ZRH (Suisse)', 'Suisse', 'Europe', '2025-07-07 08:39:00', '2025-06-20 21:01:00', 'Aller-retour', 65233.00, 71),
(186, 'DZ8747', 'Etihad Airways', 'Aéroport d’Alger - Houari Boumediene', 'Barcelone BCN (Espagne)', 'Espagne', 'Europe', '2025-07-04 22:32:00', '2025-08-27 22:02:00', 'Aller-retour', 37704.00, 176),
(187, 'DZ3525', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-06-24 14:12:00', '2025-06-29 17:54:00', 'Aller-retour', 58636.00, 90),
(188, 'DZ5891', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Casablanca CMN (Maroc)', 'Maroc', 'Afrique', '2025-08-27 17:49:00', '2025-09-01 06:29:00', 'Aller-retour', 17352.00, 33),
(189, 'DZ8282', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-01 07:37:00', '2025-06-17 21:58:00', 'Aller simple', 25605.00, 131),
(190, 'DZ7520', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-07-03 17:39:00', '2025-06-07 04:48:00', 'Aller simple', 16958.00, 44),
(191, 'DZ1603', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-07-21 12:57:00', '2025-07-01 10:25:00', 'Aller-retour', 48467.00, 193),
(192, 'DZ3477', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Afrique', '2025-06-19 22:03:00', '2025-07-19 09:26:00', 'Aller-retour', 43625.00, 40),
(193, 'DZ9863', 'Etihad Airways', 'Aéroport de Béjaïa - Soummam', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-08-23 05:27:00', '2025-07-24 11:43:00', 'Aller-retour', 31932.00, 196),
(194, 'DZ9578', 'Lufthansa', 'Aéroport d’Annaba - Rabah Bitat', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-06-01 03:38:00', '2025-08-17 02:08:00', 'Aller simple', 33576.00, 31),
(195, 'DZ2258', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-06-24 18:10:00', '2025-07-21 22:18:00', 'Aller simple', 55404.00, 237),
(196, 'DZ1101', 'Turkish Airlines', 'Aéroport d’Alger - Houari Boumediene', 'Stockholm ARN (Suède)', 'Suède', 'Europe', '2025-06-05 11:15:00', '2025-08-17 08:14:00', 'Aller simple', 11961.00, 209),
(197, 'DZ1139', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-08-22 15:32:00', '2025-08-19 08:22:00', 'Aller simple', 72964.00, 194),
(198, 'DZ4370', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-07-02 00:05:00', '2025-07-09 14:51:00', 'Aller-retour', 74562.00, 136),
(199, 'DZ1023', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-10 02:13:00', '2025-08-15 08:26:00', 'Aller simple', 69614.00, 47),
(200, 'DZ2398', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Tel Aviv TLV (Israël)', 'Israël', 'Asie', '2025-07-24 01:38:00', '2025-09-01 21:38:00', 'Aller-retour', 41029.00, 74),
(201, 'DZ8555', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-07-02 04:53:00', '2025-08-30 22:54:00', 'Aller-retour', 88190.00, 125),
(202, 'DZ3044', 'Singapore Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Europe', '2025-08-20 22:02:00', '2025-07-10 21:12:00', 'Aller simple', 33730.00, 166),
(203, 'DZ2874', 'Tassili Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-24 12:45:00', '2025-06-29 04:08:00', 'Aller-retour', 53158.00, 215),
(204, 'DZ6290', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Asie', '2025-07-06 22:27:00', '2025-07-23 02:03:00', 'Aller-retour', 28837.00, 98),
(205, 'DZ8695', 'Qatar Airways', 'Aéroport d’Alger - Houari Boumediene', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-08-11 17:15:00', '2025-06-19 23:30:00', 'Aller simple', 56260.00, 61),
(206, 'DZ9901', 'Lufthansa', 'Aéroport d’Annaba - Rabah Bitat', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-06-10 17:19:00', '2025-07-17 08:18:00', 'Aller-retour', 25679.00, 114),
(207, 'DZ4054', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-06-28 16:29:00', '2025-07-15 15:56:00', 'Aller-retour', 51395.00, 189),
(208, 'DZ1595', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Jakarta CGK (Indonésie)', 'Indonésie', 'Asie', '2025-07-11 19:42:00', '2025-06-15 12:11:00', 'Aller simple', 50376.00, 149),
(209, 'DZ2908', 'Emirates', 'Aéroport de Constantine - Mohamed Boudiaf', 'Madrid MAD (Espagne)', 'Espagne', 'Europe', '2025-06-23 04:06:00', '2025-06-11 00:54:00', 'Aller-retour', 70988.00, 228),
(210, 'DZ2975', 'Qatar Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Buenos Aires EZE (Argentine)', 'Argentine', 'Amérique du Sud', '2025-07-14 14:39:00', '2025-07-15 03:25:00', 'Aller simple', 61720.00, 58),
(211, 'DZ7390', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-07-01 11:26:00', '2025-08-11 07:19:00', 'Aller-retour', 63258.00, 176),
(212, 'DZ3971', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Berlin BER (Allemagne)', 'Allemagne', 'Europe', '2025-07-16 09:24:00', '2025-08-30 10:22:00', 'Aller-retour', 76018.00, 32),
(213, 'DZ6257', 'Singapore Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Athens ATH (Grèce)', 'Grèce', 'Europe', '2025-07-05 08:45:00', '2025-07-30 22:46:00', 'Aller-retour', 45179.00, 129),
(214, 'DZ9455', 'Qatar Airways', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-06-15 07:01:00', '2025-06-29 03:03:00', 'Aller-retour', 77392.00, 174),
(215, 'DZ7850', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-07-28 12:40:00', '2025-08-21 05:36:00', 'Aller simple', 13189.00, 51),
(216, 'DZ4354', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-06-14 14:33:00', '2025-06-10 10:01:00', 'Aller simple', 79881.00, 132),
(217, 'DZ7431', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Europe', '2025-08-02 20:15:00', '2025-08-09 18:46:00', 'Aller simple', 70439.00, 249),
(218, 'DZ7521', 'EgyptAir', 'Aéroport de Béjaïa - Soummam', 'Bucharest OTP (Roumanie)', 'Roumanie', 'Europe', '2025-07-16 20:44:00', '2025-06-16 13:20:00', 'Aller simple', 41208.00, 178),
(219, 'DZ2863', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-07-27 12:46:00', '2025-07-03 05:15:00', 'Aller simple', 64250.00, 64),
(220, 'DZ7788', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-27 03:56:00', '2025-06-24 08:03:00', 'Aller simple', 52241.00, 134),
(221, 'DZ8143', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Paris CDG (France)', 'France', 'Europe', '2025-07-15 02:03:00', '2025-08-30 17:38:00', 'Aller simple', 86433.00, 133),
(222, 'DZ5442', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-27 23:58:00', '2025-06-03 03:42:00', 'Aller simple', 67897.00, 47),
(223, 'DZ3072', 'American Airlines', 'Aéroport de Béjaïa - Soummam', 'Riyad RUH (Arabie Saoudite)', 'Arabie Saoudite', 'Afrique', '2025-08-05 17:25:00', '2025-09-02 15:16:00', 'Aller simple', 83188.00, 226),
(224, 'DZ7570', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Asie', '2025-08-18 15:39:00', '2025-07-12 21:18:00', 'Aller-retour', 14157.00, 165),
(225, 'DZ9235', 'Etihad Airways', 'Aéroport de Béjaïa - Soummam', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-08-16 13:20:00', '2025-06-06 02:31:00', 'Aller-retour', 86788.00, 82),
(226, 'DZ3751', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-06-21 15:34:00', '2025-08-24 20:30:00', 'Aller-retour', 83863.00, 207),
(227, 'DZ3428', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-07-21 10:33:00', '2025-07-24 20:41:00', 'Aller-retour', 20156.00, 223),
(228, 'DZ1181', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-07-15 05:41:00', '2025-08-19 21:04:00', 'Aller simple', 52967.00, 178),
(229, 'DZ7973', 'Singapore Airlines', 'Aéroport de Béjaïa - Soummam', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-08-12 05:42:00', '2025-08-30 11:37:00', 'Aller-retour', 71650.00, 63),
(230, 'DZ4990', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'New York JFK (USA)', 'USA', 'Afrique', '2025-08-31 02:36:00', '2025-08-07 11:29:00', 'Aller simple', 71918.00, 223),
(231, 'DZ1655', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Warsaw WAW (Pologne)', 'Pologne', 'Europe', '2025-08-02 13:41:00', '2025-08-23 03:13:00', 'Aller-retour', 74405.00, 181),
(232, 'DZ1343', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-08-01 02:25:00', '2025-08-24 00:33:00', 'Aller-retour', 13208.00, 187),
(233, 'DZ5201', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Paris CDG (France)', 'France', 'Europe', '2025-07-07 13:36:00', '2025-07-06 21:25:00', 'Aller simple', 81814.00, 97),
(234, 'DZ8514', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'New York JFK (USA)', 'USA', 'Amérique du Sud', '2025-07-23 15:29:00', '2025-07-22 02:57:00', 'Aller-retour', 62423.00, 113),
(235, 'DZ9359', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-08-04 21:20:00', '2025-06-10 04:50:00', 'Aller-retour', 57086.00, 210),
(236, 'DZ3948', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-06-02 02:27:00', '2025-06-02 15:12:00', 'Aller simple', 67557.00, 145),
(238, 'DZ3933', 'Emirates', 'Aéroport d’Alger - Houari Boumediene', 'Moscou SVO (Russie)', 'Russie', 'Europe', '2025-08-18 04:25:00', '2025-07-29 13:05:00', 'Aller-retour', 62656.00, 140),
(239, 'DZ5859', 'Tassili Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-06-25 18:09:00', '2025-07-08 11:19:00', 'Aller-retour', 35293.00, 34),
(240, 'DZ2373', 'Etihad Airways', 'Aéroport d’Alger - Houari Boumediene', 'Madrid MAD (Espagne)', 'Espagne', 'Europe', '2025-08-14 02:00:00', '2025-08-13 16:09:00', 'Aller simple', 26327.00, 62),
(241, 'DZ2172', 'Turkish Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Riyad RUH (Arabie Saoudite)', 'Arabie Saoudite', 'Europe', '2025-06-04 07:32:00', '2025-08-11 02:18:00', 'Aller-retour', 35265.00, 178),
(242, 'DZ4843', 'Tassili Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-19 16:48:00', '2025-09-05 11:28:00', 'Aller simple', 88468.00, 201),
(243, 'DZ9573', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-08-05 19:53:00', '2025-06-07 09:58:00', 'Aller simple', 56872.00, 34),
(244, 'DZ4189', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Moscou SVO (Russie)', 'Russie', 'Europe', '2025-06-17 05:41:00', '2025-08-14 16:09:00', 'Aller simple', 29633.00, 32),
(245, 'DZ3835', 'Lufthansa', 'Aéroport de Tamanrasset - Aguenar', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-08-27 22:47:00', '2025-06-19 11:56:00', 'Aller simple', 57200.00, 144),
(246, 'DZ8391', 'Lufthansa', 'Aéroport d’Oran - Ahmed Ben Bella', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-07-17 23:18:00', '2025-08-02 07:39:00', 'Aller-retour', 20887.00, 212),
(247, 'DZ7571', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-07-25 16:38:00', '2025-06-22 23:19:00', 'Aller simple', 15695.00, 51),
(248, 'DZ3413', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-08-02 18:49:00', '2025-08-19 11:05:00', 'Aller-retour', 11085.00, 98),
(249, 'DZ5705', 'EgyptAir', 'Aéroport de Tamanrasset - Aguenar', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-17 08:15:00', '2025-06-28 10:22:00', 'Aller-retour', 11539.00, 66),
(250, 'DZ8030', 'Emirates', 'Aéroport de Tamanrasset - Aguenar', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-27 17:49:00', '2025-08-24 00:28:00', 'Aller-retour', 28676.00, 205),
(251, 'DZ3628', 'Air Algérie', 'Aéroport de Constantine - Mohamed Boudiaf', 'Tunis TUN (Tunisie)', 'Tunisie', 'Afrique', '2025-06-30 22:39:00', '2025-07-17 12:06:00', 'Aller-retour', 64733.00, 68),
(252, 'DZ8411', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-07-13 15:45:00', '2025-08-25 05:23:00', 'Aller-retour', 51783.00, 48),
(253, 'DZ8629', 'American Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Dubaï DXB (É.A.U.)', 'Émirats Arabes Unis', 'Europe', '2025-06-29 06:22:00', '2025-06-16 08:23:00', 'Aller-retour', 73113.00, 181),
(254, 'DZ1710', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Paris CDG (France)', 'France', 'Europe', '2025-07-01 00:09:00', '2025-08-10 23:45:00', 'Aller simple', 11036.00, 179),
(255, 'DZ4162', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-07-18 03:07:00', '2025-06-27 20:36:00', 'Aller simple', 46819.00, 235),
(256, 'DZ3515', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-06-28 11:25:00', '2025-08-12 11:10:00', 'Aller-retour', 64007.00, 162),
(257, 'DZ9949', 'Tassili Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Tel Aviv TLV (Israël)', 'Israël', 'Afrique', '2025-06-11 19:44:00', '2025-07-04 04:05:00', 'Aller simple', 57322.00, 65),
(258, 'DZ1291', 'EgyptAir', 'Aéroport d’Oran - Ahmed Ben Bella', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-08-27 00:12:00', '2025-09-04 08:42:00', 'Aller-retour', 50871.00, 172),
(260, 'DZ1469', 'Turkish Airlines', 'Aéroport de Béjaïa - Soummam', 'Amsterdam AMS (Pays-Bas)', 'Pays-Bas', 'Europe', '2025-06-11 09:33:00', '2025-08-06 14:07:00', 'Aller simple', 29003.00, 104),
(261, 'DZ9730', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Bucharest OTP (Roumanie)', 'Roumanie', 'Europe', '2025-07-07 10:56:00', '2025-07-11 07:12:00', 'Aller simple', 68015.00, 221),
(262, 'DZ2566', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-31 03:47:00', '2025-07-18 23:30:00', 'Aller simple', 79469.00, 119),
(263, 'DZ4894', 'American Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Los Angeles LAX (USA)', 'USA', 'Europe', '2025-07-13 21:00:00', '2025-07-07 21:22:00', 'Aller-retour', 31539.00, 196),
(264, 'DZ9786', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Dublin DUB (Irlande)', 'Irlande', 'Europe', '2025-07-23 02:43:00', '2025-07-09 17:31:00', 'Aller simple', 66674.00, 123),
(265, 'DZ9928', 'EgyptAir', 'Aéroport de Constantine - Mohamed Boudiaf', 'Johannesburg JNB (A.-S.)', 'Afrique du Sud', 'Afrique', '2025-07-05 17:32:00', '2025-07-17 18:30:00', 'Aller simple', 73770.00, 147),
(266, 'DZ3602', 'Singapore Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Francfort FRA (Allemagne)', 'Allemagne', 'Europe', '2025-06-14 07:03:00', '2025-07-11 17:35:00', 'Aller-retour', 56377.00, 181),
(267, 'DZ7360', 'Emirates', 'Aéroport d’Oran - Ahmed Ben Bella', 'Zurich ZRH (Suisse)', 'Suisse', 'Europe', '2025-06-21 04:21:00', '2025-06-20 20:45:00', 'Aller-retour', 74893.00, 36),
(268, 'DZ7355', 'Emirates', 'Aéroport d’Alger - Houari Boumediene', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-07-22 05:26:00', '2025-07-21 08:14:00', 'Aller-retour', 82487.00, 243),
(269, 'DZ2270', 'Etihad Airways', 'Aéroport d’Annaba - Rabah Bitat', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-08-19 03:04:00', '2025-09-03 14:07:00', 'Aller-retour', 80788.00, 127),
(270, 'DZ6023', 'Emirates', 'Aéroport d’Annaba - Rabah Bitat', 'Vienna VIE (Autriche)', 'Autriche', 'Europe', '2025-06-29 02:34:00', '2025-07-23 01:39:00', 'Aller-retour', 40402.00, 92),
(271, 'DZ3579', 'Lufthansa', 'Aéroport d’Alger - Houari Boumediene', 'Mexico City MEX (Mexique)', 'Mexique', 'Amérique du Nord', '2025-06-26 16:41:00', '2025-07-06 14:05:00', 'Aller-retour', 22333.00, 43),
(272, 'DZ8625', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Lima LIM (Pérou)', 'Pérou', 'Amérique du Sud', '2025-06-24 14:14:00', '2025-07-08 03:35:00', 'Aller-retour', 70953.00, 74),
(273, 'DZ7610', 'Air Algérie', 'Aéroport d’Alger - Houari Boumediene', 'Sydney SYD (Australie)', 'Australie', 'Océanie', '2025-06-28 18:57:00', '2025-07-20 11:03:00', 'Aller-retour', 41028.00, 30),
(274, 'DZ8669', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-24 16:43:00', '2025-07-20 08:18:00', 'Aller simple', 69657.00, 57),
(275, 'DZ4408', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Stockholm ARN (Suède)', 'Suède', 'Europe', '2025-07-08 15:03:00', '2025-07-05 13:46:00', 'Aller-retour', 14910.00, 199),
(276, 'DZ7071', 'Air Algérie', 'Aéroport d’Oran - Ahmed Ben Bella', 'New York JFK (USA)', 'USA', 'Europe', '2025-06-30 17:45:00', '2025-07-29 14:17:00', 'Aller simple', 39228.00, 49),
(277, 'DZ4139', 'Emirates', 'Aéroport de Constantine - Mohamed Boudiaf', 'Singapour SIN (Singapour)', 'Singapour', 'Asie', '2025-08-04 14:58:00', '2025-06-02 02:28:00', 'Aller simple', 20321.00, 113),
(278, 'DZ5560', 'Qatar Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'New York JFK (USA)', 'USA', 'Océanie', '2025-06-15 20:45:00', '2025-06-27 00:15:00', 'Aller-retour', 52403.00, 162),
(279, 'DZ4775', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Londres LHR (R.-U.)', 'R.-U.', 'Asie', '2025-08-08 15:03:00', '2025-06-28 05:24:00', 'Aller simple', 11000.00, 152),
(280, 'DZ7738', 'Air Algérie', 'Aéroport d’Alger - Houari Boumediene', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-08-28 14:04:00', '2025-07-29 17:56:00', 'Aller-retour', 47078.00, 155),
(281, 'DZ5222', 'EgyptAir', 'Aéroport de Tamanrasset - Aguenar', 'Oslo OSL (Norvège)', 'Norvège', 'Europe', '2025-08-24 23:05:00', '2025-07-28 11:40:00', 'Aller-retour', 13945.00, 226),
(282, 'DZ3803', 'Singapore Airlines', 'Aéroport de Constantine - Mohamed Boudiaf', 'Amsterdam AMS (Pays-Bas)', 'Pays-Bas', 'Europe', '2025-08-22 19:27:00', '2025-08-18 18:17:00', 'Aller simple', 79534.00, 46),
(283, 'DZ7959', 'EgyptAir', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-07-16 19:34:00', '2025-07-14 07:21:00', 'Aller simple', 76041.00, 239),
(284, 'DZ3495', 'Lufthansa', 'Aéroport de Béjaïa - Soummam', 'Casablanca CMN (Maroc)', 'Maroc', 'Afrique', '2025-07-08 17:24:00', '2025-08-23 00:33:00', 'Aller simple', 35295.00, 77),
(285, 'DZ2230', 'Air Algérie', 'Aéroport de Tamanrasset - Aguenar', 'Mumbai BOM (Inde)', 'Inde', 'Asie', '2025-06-27 06:33:00', '2025-09-04 03:49:00', 'Aller simple', 38677.00, 135),
(286, 'DZ3916', 'Tassili Airlines', 'Aéroport de Béjaïa - Soummam', 'Rome FCO (Italie)', 'Italie', 'Europe', '2025-07-16 04:22:00', '2025-07-08 16:18:00', 'Aller-retour', 48058.00, 158),
(287, 'DZ5528', 'Etihad Airways', 'Aéroport d’Oran - Ahmed Ben Bella', 'Londres LHR (R.-U.)', 'R.-U.', 'Europe', '2025-07-16 03:12:00', '2025-08-03 11:27:00', 'Aller simple', 69501.00, 200),
(288, 'DZ6876', 'American Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-25 17:52:00', '2025-06-20 04:27:00', 'Aller simple', 61823.00, 203),
(289, 'DZ9964', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-07-02 04:57:00', '2025-06-25 04:13:00', 'Aller simple', 62169.00, 119),
(290, 'DZ1789', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'São Paulo GRU (Brésil)', 'Brésil', 'Amérique du Sud', '2025-08-04 10:06:00', '2025-06-28 19:26:00', 'Aller simple', 26787.00, 80),
(291, 'DZ5680', 'American Airlines', 'Aéroport de Béjaïa - Soummam', 'Tokyo NRT (Japon)', 'Japon', 'Asie', '2025-06-21 13:09:00', '2025-06-14 22:31:00', 'Aller simple', 44296.00, 196),
(292, 'DZ5525', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-06-22 01:32:00', '2025-07-27 04:14:00', 'Aller-retour', 38830.00, 195),
(293, 'DZ7041', 'Tassili Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Shanghai PVG (Chine)', 'Chine', 'Asie', '2025-07-31 06:18:00', '2025-08-15 00:40:00', 'Aller-retour', 51659.00, 132),
(294, 'DZ7936', 'Emirates', 'Aéroport de Béjaïa - Soummam', 'Toronto YYZ (Canada)', 'Canada', 'Amérique du Nord', '2025-06-03 08:40:00', '2025-07-04 13:50:00', 'Aller simple', 32549.00, 220),
(295, 'DZ5262', 'Etihad Airways', 'Aéroport de Constantine - Mohamed Boudiaf', 'Nairobi NBO (Kenya)', 'Kenya', 'Afrique', '2025-07-26 12:42:00', '2025-09-03 21:24:00', 'Aller-retour', 15426.00, 126),
(296, 'DZ9887', 'EgyptAir', 'Aéroport d’Alger - Houari Boumediene', 'Copenhagen CPH (Danemark)', 'Danemark', 'Europe', '2025-06-28 12:41:00', '2025-08-25 11:43:00', 'Aller simple', 11034.00, 167),
(297, 'DZ1741', 'Lufthansa', 'Aéroport de Constantine - Mohamed Boudiaf', 'Prague PRG (République tchèque)', 'République tchèque', 'Europe', '2025-06-09 01:05:00', '2025-06-26 18:15:00', 'Aller simple', 25145.00, 230),
(298, 'DZ9958', 'Turkish Airlines', 'Aéroport d’Oran - Ahmed Ben Bella', 'Hong Kong HKG (R.A.S.)', 'Chine', 'Europe', '2025-06-10 11:04:00', '2025-08-29 08:57:00', 'Aller-retour', 44966.00, 107),
(299, 'DZ5107', 'Turkish Airlines', 'Aéroport de Tamanrasset - Aguenar', 'Istanbul IST (Turquie)', 'Turquie', 'Asie', '2025-07-20 22:59:00', '2025-06-24 22:07:00', 'Aller-retour', 86558.00, 43),
(300, 'DZ4856', 'American Airlines', 'Aéroport d’Annaba - Rabah Bitat', 'Brussels BRU (Belgique)', 'Belgique', 'Europe', '2025-08-05 22:38:00', '2025-07-10 22:29:00', 'Aller-retour', 78052.00, 95);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `fk_client_user` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `fk_fact_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gestionnaire`
--
ALTER TABLE `gestionnaire`
  ADD CONSTRAINT `fk_gest_user` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_nom_hotel` FOREIGN KEY (`nom_hotel`) REFERENCES `hotel` (`nom`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservation_vol` FOREIGN KEY (`numero_vol`) REFERENCES `vol` (`numero_vol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
