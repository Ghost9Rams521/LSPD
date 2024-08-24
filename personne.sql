-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 24 août 2024 à 20:41
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `personne`
--

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `id_personne` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) NOT NULL,
  `Prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Age` int NOT NULL,
  `Profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Organisation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Dossier` varchar(255) NOT NULL,
  `Photo` varchar(1000) NOT NULL,
  `Gang` varchar(255) NOT NULL,
  PRIMARY KEY (`id_personne`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`id_personne`, `Nom`, `Prenom`, `Age`, `Profession`, `Organisation`, `Dossier`, `Photo`, `Gang`) VALUES
(1, 'rex', 'broche', 18, 'pompier', 'NaN', 'Bonjour en vrai sa vas ', 'NaN', ''),
(2, 'df', 'df', 0, 'df', 'df', 'sdfdf', 'sdf', 'df'),
(3, 'HIRSH', 'SIMBA', 20, 'NaN', 'NaN', 'Rechercher \r\n-Possession de drogue\r\n-Possession d\'arme illégale\r\n-Outrage a agent', 'uploads/image.png', 'NaN'),
(4, 'df', 'ddfdf', 235, 'sdsdv', 'sdvsdv', 'sdvsdvsd', 'image.png', 'sdvsdv');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
