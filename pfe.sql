-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 25, 2025 at 01:28 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int UNSIGNED NOT NULL,
  `niveau` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admin_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int UNSIGNED NOT NULL,
  `adress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `num_passeport` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_client_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int UNSIGNED NOT NULL,
  `poste` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departement` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_gest_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

DROP TABLE IF EXISTS `hotel`;
CREATE TABLE IF NOT EXISTS `hotel` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` int UNSIGNED NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ville` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etoiles` tinyint DEFAULT NULL,
  `chambres` json DEFAULT NULL,
  `prix_par_nuit` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `fk_hotel_reservation` (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `nom` varchar(100) NOT NULL,
  `continent` varchar(50) NOT NULL,
  `code` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_pays_nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(73, 'Samoa', 'Océanie', 'WS', '2025-04-25 01:07:00', '2025-04-25 01:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int UNSIGNED NOT NULL,
  `date_reservation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_id` int UNSIGNED NOT NULL,
  `montant_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `est_paye` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_res_client` (`client_id`),
  KEY `fk_res_statut` (`statut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `fk_utilisateur_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vol`
--

DROP TABLE IF EXISTS `vol`;
CREATE TABLE IF NOT EXISTS `vol` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` int UNSIGNED NOT NULL,
  `numero_vol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `compagnie_aerienne` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_depart` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aeroport_arrivee` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `type_vol` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `places_disponibles` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_vol_reservation` (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `fk_client_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `fk_fact_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gestionnaire`
--
ALTER TABLE `gestionnaire`
  ADD CONSTRAINT `fk_gest_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hotel`
--
ALTER TABLE `hotel`
  ADD CONSTRAINT `fk_hotel_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notif_gest` FOREIGN KEY (`gestionnaire_id`) REFERENCES `gestionnaire` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `fk_paiement_met` FOREIGN KEY (`methode_paiement_id`) REFERENCES `methode_paiement` (`id`),
  ADD CONSTRAINT `fk_paiement_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_res_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `fk_res_statut` FOREIGN KEY (`statut_id`) REFERENCES `statue_reservation` (`id`);

--
-- Constraints for table `transport`
--
ALTER TABLE `transport`
  ADD CONSTRAINT `fk_tr_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`),
  ADD CONSTRAINT `fk_tr_type` FOREIGN KEY (`type_transport_id`) REFERENCES `type_transport` (`id`);

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `vol`
--
ALTER TABLE `vol`
  ADD CONSTRAINT `fk_vol_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
