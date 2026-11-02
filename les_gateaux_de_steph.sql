-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 16 oct. 2026 à 10:34
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `les_gateaux_de_steph`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id_art` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` float NOT NULL,
  `img` varchar(100) NOT NULL,
  `composition` text NOT NULL,
  `fin_chrono` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_art`, `nom`, `quantite`, `prix`, `img`, `composition`, `fin_chrono`) VALUES
(1, 'Cookie', 4, 3.5, 'images/cookie.jpg', 'farine de blé, beurre, sucre, chocolat, œuf', NULL),
(2, 'Cheesecake', 0, 15, 'images/cheesecake.jpg', 'fromage frais, crème, sucre, œuf, spéculoos, beurre, fruits rouges (décoration)', NULL),
(3, 'Tiramisu', 2, 15, 'images/tiramisu.jpg', 'mascarpone, café, sucre, œuf, biscuits cuillère, cacao (décoration)', NULL),
(4, 'Forêt noire', 8, 25, 'images/foret_noire.jpg', 'farine de blé, crème, sucre, chocolat, œuf, beurre, cerises, kirsch (arôme)', NULL),
(5, 'Tarte aux fraises', 3, 20, 'images/tarte.jpg', 'fraises, creme, farine, beurre, sucre', NULL),
(6, 'Éclair au chocolat', 5, 4, 'images/eclair.jpg', 'farine, lait, chocolat, beurre, sucre, œufs', NULL),
(7, 'Paris-Brest', 2, 6, 'images/paris-brest.jpg', 'farine, beurre, œufs, sucre, lait, amandes', NULL),
(8, 'Opéra', 3, 20, 'images/opera.jpg', 'chocolat, farine, beurre, sucre, œufs, café', NULL),
(9, 'Tarte tropézienne', 3, 18, 'images/tarte_tropezienne.jpg', 'farine, crème, beurre, sucre, œufs, lait, levure', NULL),
(10, 'Flan pâtissier', 4, 4, 'images/flan.jpg', 'lait, farine, sucre, œufs, crème, vanille', NULL),
(11, 'Fraisier', 2, 22, 'images/fraisier.jpg', 'fraises, farine, lait, sucre, œufs, beurre, crème', NULL),
(12, 'Mille-feuille', 4, 4, 'images/mille-feuille.jpg', 'farine, beurre, lait, sucre, œufs, vanille', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `adresse` text NOT NULL,
  `numero` varchar(20) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `mdp` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `adresse`, `numero`, `mail`, `mdp`) VALUES
(1, 'Sbircea', 'Stephanie', 'rue des alouettes', '0666666666', 's.s@gmail.com', '$2y$10$.nPeK/DTEgVd1vX.NIlkduQUPqFUVvzgZagOIL3PYZWBPhyTf5HsW'),
(2, 'Ten', 'Ben', '1 rue Béole', '0000000000', 'bt@gmail.com', '$2y$10$l7V3QnkRElYleMuT6CNOh.98Le7aKWeQN3KWu9bfmq5SsqKFc8j4i');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_art` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `envoi` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_art`, `id_client`, `quantite`, `envoi`) VALUES
(1, 2, 1, 3, 0),
(2, 1, 1, 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `texte` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservation` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_art` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_reservation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id_reservation`, `id_client`, `id_art`, `quantite`, `date_reservation`) VALUES
(1, 2, 1, 1, '2026-10-16 12:32:40');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_art`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_art` (`id_art`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_client` (`id_client`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservation`),
  ADD UNIQUE KEY `uniq_client_art` (`id_client`,`id_art`),
  ADD KEY `fk_res_art` (`id_art`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_art` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_client` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_art` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_client` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
