-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 28, 2026 at 08:04 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `abonne`
--

DROP TABLE IF EXISTS `abonne`;
CREATE TABLE IF NOT EXISTS `abonne` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `commune` varchar(100) DEFAULT 'Nzadi',
  `numero_compteur` varchar(50) NOT NULL,
  `statut` enum('Actif','Inactif') NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_compteur` (`numero_compteur`),
  KEY `idx_abonne_compteur` (`numero_compteur`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `abonne`
--

INSERT INTO `abonne` (`id`, `nom`, `email`, `telephone`, `mdp`, `adresse`, `commune`, `numero_compteur`, `statut`, `code`, `created_at`) VALUES
(1, 'Joseph Phanzu', 'gloreensmith@gmail.com', '0820908486', 'Joseph089', 'Caserne 1518', 'Nzadi', 'NH9877', 'Actif', '594b4b9dc8ebd800dbcd8c64f0c32861', '2026-05-28 11:34:51'),
(2, 'Joe Diakota', 'joediakota8@gmail.com', '0897656432', 'Diakota089', 'Manzambi', 'Nzadi', 'BG7657', 'Actif', 'bb240cb3d1c21d8d4815759312182e9e', '2026-05-28 12:12:11'),
(3, 'Nathan Yubu', 'nathan@gmail.com', '0896545321', 'Nathant089', 'Sanpa', 'Nzadi', 'NJ8775', 'Actif', '5d73028f36519f98c434bdd170e00ec1', '2026-05-28 12:13:21'),
(4, 'Clark Mbianga', 'clarkmbianga@gmail.com', '0899266979', 'Clark089', 'kinshasa', 'Nzadi', 'N76766', 'Actif', '0d5c66ab13743603aa0dc3667d371eb5', '2026-05-28 12:41:10'),
(5, 'Joel Mayindu', 'joelmayindu@gmail.com', '0897654321', 'Joel089', 'Saico', 'Nzadi', 'NH6567', 'Actif', '8ed864cd2e60d42a3dea89bcc3d2fd90', '2026-05-28 17:34:14');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mdp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `code` varchar(255) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `nom`, `email`, `mdp`, `code`, `create_at`) VALUES
(1, 'snel congo', 'snelboma@snel.cd', '$2y$10$RQgP6/j55QAFl2h8nlLJfeFy/wcmQVnkBK0mtmBIs4.Lhv1ABHsDa', '6a34c886bd24d9e6476075c0546cbb17', '2026-04-09 20:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `consommation`
--

DROP TABLE IF EXISTS `consommation`;
CREATE TABLE IF NOT EXISTS `consommation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_abonne` varchar(255) NOT NULL,
  `mois` varchar(20) NOT NULL,
  `annee` int NOT NULL,
  `index_ancien` int NOT NULL,
  `index_nouveau` int NOT NULL,
  `consommation` int GENERATED ALWAYS AS ((`index_nouveau` - `index_ancien`)) STORED,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_consommation_abonne` (`code_abonne`(250))
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consommation`
--

INSERT INTO `consommation` (`id`, `code_abonne`, `mois`, `annee`, `index_ancien`, `index_nouveau`, `code`, `created_at`) VALUES
(6, '0d5c66ab13743603aa0dc3667d371eb5', 'Mars', 2026, 10, 25, '50efa6a949c7ae6279b68ce6ad6e50bc', '2026-05-28 15:31:42'),
(7, '0d5c66ab13743603aa0dc3667d371eb5', 'Avril', 2026, 25, 50, 'b430c7a0fa2c322c1d6c79aa7440a4c4', '2026-05-28 15:32:33'),
(8, '0d5c66ab13743603aa0dc3667d371eb5', 'Mai', 2026, 50, 13, '57485f19b631a85f2f2c2006287aeff8', '2026-05-28 15:33:36'),
(9, 'bb240cb3d1c21d8d4815759312182e9e', 'Avril', 2026, 1, 10, '4eacd56b1e0ba0d4affd501ec779182a', '2026-05-28 15:35:01'),
(10, '5d73028f36519f98c434bdd170e00ec1', 'Mars', 2026, 10, 45, '2a3663efa3f97fba27bd6e0ca42bf11f', '2026-05-28 15:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_conso` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('Non payée','Payée') DEFAULT 'Non payée',
  `date_facture` date NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_facture_abonne` (`code_conso`(250)),
  KEY `idx_facture_statut` (`statut`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facture`
--

INSERT INTO `facture` (`id`, `code_conso`, `montant`, `statut`, `date_facture`, `code`, `created_at`) VALUES
(1, '57485f19b631a85f2f2c2006287aeff8', -3829.50, '', '0000-00-00', '2c9dc915707e2896a41f4bad800fc240', '2026-05-28 15:33:36'),
(2, '4eacd56b1e0ba0d4affd501ec779182a', 931.50, '', '0000-00-00', '727c9ebce9fd737edbe327655c243fec', '2026-05-28 15:35:01'),
(3, '2a3663efa3f97fba27bd6e0ca42bf11f', 24089.63, '', '0000-00-00', '1146bc345595252485791aa77ed5e15b', '2026-05-28 15:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_facture` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  `methode` varchar(50) DEFAULT NULL,
  `reference_transaction` varchar(100) DEFAULT NULL,
  `statut` enum('En attente','Réussi','Échoué') DEFAULT 'En attente',
  PRIMARY KEY (`id`),
  KEY `fk_paiement_facture` (`code_facture`(250)),
  KEY `idx_paiement_statut` (`statut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `peut_connecter` int NOT NULL,
  `peut_gerer` int NOT NULL,
  `code_user` varchar(255) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `type`, `peut_connecter`, `peut_gerer`, `code_user`, `create_at`) VALUES
(4, 'Abonne', 1, 1, '594b4b9dc8ebd800dbcd8c64f0c32861', '2026-05-28 11:34:51'),
(5, 'Abonne', 1, 1, 'bb240cb3d1c21d8d4815759312182e9e', '2026-05-28 12:12:11'),
(6, 'Abonne', 1, 1, '5d73028f36519f98c434bdd170e00ec1', '2026-05-28 12:13:21'),
(7, 'Abonne', 1, 1, '0d5c66ab13743603aa0dc3667d371eb5', '2026-05-28 12:41:10'),
(8, 'Abonne', 1, 1, '8ed864cd2e60d42a3dea89bcc3d2fd90', '2026-05-28 17:34:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
