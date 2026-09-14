-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql:3306
-- Généré le : lun. 14 sep. 2026 à 08:33
-- Version du serveur : 8.4.9
-- Version de PHP : 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `app`
--

-- --------------------------------------------------------

--
-- Structure de la table `adress`
--

CREATE TABLE `adress` (
  `id` int NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `cp` varchar(255) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `adress`
--

INSERT INTO `adress` (`id`, `adresse`, `cp`, `type`, `user_id`) VALUES
(1, '18 rue des Camélias', '44000', NULL, 115),
(2, '27 avenue des Chênes', '44100', NULL, 101),
(3, '5 rue du Pré Fleuri', '44200', NULL, 90),
(4, '42 boulevard des Nations', '44300', NULL, 84),
(5, '11 rue des Hortensias', '44400', NULL, 87),
(6, '36 avenue de la Gare', '44500', NULL, 120),
(7, '9 rue des Érables', '35000', NULL, 85),
(8, '54 rue du Clos Saint-Pierre', '35200', NULL, 116),
(9, '16 avenue des Peupliers', '35300', NULL, 105),
(10, '28 rue des Artisans', '35400', NULL, 101),
(11, '7 rue de la Fontaine', '49000', NULL, 107),
(12, '33 avenue des Vignes', '49100', NULL, 99),
(13, '61 rue des Tonneliers', '49300', NULL, 100),
(14, '14 rue du Pont Neuf', '37000', NULL, 84),
(15, '48 avenue des Minimes', '37100', NULL, 107),
(16, '22 rue des Violettes', '33000', NULL, 122),
(17, '39 boulevard Alfred Daney', '33100', NULL, 89),
(18, '6 rue des Pêcheurs', '17000', NULL, 89),
(19, '51 avenue de la Rochelle', '17100', NULL, 92),
(20, '13 rue des Cerisiers', '75015', NULL, 116),
(21, '74 rue du Faubourg', '75011', NULL, 99),
(22, '31 avenue des Lumières', '69003', NULL, 117),
(23, '8 rue des Canuts', '69004', NULL, 121),
(24, '45 rue de la République', '69002', NULL, 95),
(25, '19 avenue Jean Moulin', '31000', NULL, 92),
(26, '63 rue des Carmes', '45000', NULL, 111),
(27, '26 rue des Forges', '67000', NULL, 123),
(28, '10 avenue du Rhin', '67100', NULL, 90),
(29, '37 rue des Tuileries', '59000', NULL, 110),
(30, '55 boulevard Vauban', '59800', NULL, 104),
(31, '4 rue des Lavandières', '13001', NULL, 108),
(32, '29 avenue du Prado', '13008', NULL, 112),
(33, '17 rue des Oliviers', '34000', NULL, 94),
(34, '68 avenue de la Méditerranée', '34070', NULL, 106),
(35, '23 rue des Remparts', '66000', NULL, 87),
(36, '41 boulevard du Soleil', '06000', NULL, 85),
(37, '12 rue des Magnolias', '64000', NULL, 104),
(38, '35 avenue des Pyrénées', '65000', NULL, 112);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `nom`) VALUES
(16, 'Robes Femme'),
(17, 'Jupes Femme'),
(18, 'Pantalons Femme'),
(19, 'Shorts Femme'),
(20, 'Combinaisons Femme'),
(21, 'Chemises Femme'),
(22, 'T-shirts & Tops Femme'),
(23, 'Pulls & Sweats Femme'),
(24, 'Vestes Femme'),
(25, 'Manteaux Femme'),
(26, 'Vêtements de sport Femme'),
(27, 'Maillots de bain Femme'),
(28, 'Vêtements pour enfants'),
(29, 'Vêtements de cérémonie Femme'),
(30, 'Accessoires Femme'),
(31, 'Robes Enfant'),
(32, 'Jupes Enfant'),
(33, 'Pantalons Enfant'),
(34, 'Shorts Enfant'),
(35, 'Combinaisons Enfant'),
(36, 'Chemises Enfant'),
(37, 'T-shirts & Tops Enfant'),
(38, 'Pulls & Sweats Enfant'),
(39, 'Vestes Enfant'),
(40, 'Manteaux Enfant'),
(41, 'Vêtements de sport Enfant'),
(42, 'Maillots de bain Enfant'),
(43, 'Vêtements de cérémonie Enfant'),
(44, 'Accessoires Enfant'),
(45, 'Robes Homme'),
(46, 'Jupes Homme'),
(47, 'Pantalons Homme'),
(48, 'Shorts Homme'),
(49, 'Combinaisons Homme'),
(50, 'Chemises Homme'),
(51, 'T-shirts & Tops Homme'),
(52, 'Pulls & Sweats Homme'),
(53, 'Vestes Homme'),
(54, 'Manteaux Homme'),
(55, 'Vêtements de sport Homme'),
(56, 'Maillots de bain Homme'),
(57, 'Vêtements de cérémonie Homme'),
(58, 'Accessoires Homme');

-- --------------------------------------------------------

--
-- Structure de la table `category_group`
--

CREATE TABLE `category_group` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260910191223', '2026-09-10 19:12:30', 178),
('DoctrineMigrations\\Version20260910191605', '2026-09-10 19:16:10', 65),
('DoctrineMigrations\\Version20260910191730', '2026-09-10 19:17:39', 67);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int NOT NULL,
  `date_commande` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix_ht` double NOT NULL,
  `tva` double NOT NULL,
  `category_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products_tags`
--

CREATE TABLE `products_tags` (
  `products_id` int NOT NULL,
  `tags_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tags`
--

INSERT INTO `tags` (`id`, `nom`) VALUES
(1, 'Coton'),
(2, 'Lin'),
(3, 'Laine'),
(4, 'Soie'),
(5, 'Velours'),
(6, 'Denim'),
(7, 'Jean'),
(8, 'Satin'),
(9, 'Dentelle'),
(10, 'Cuir'),
(11, 'Similicuir'),
(12, 'Viscose'),
(13, 'Polyester'),
(14, 'Cachemire'),
(15, 'Mousseline'),
(16, 'Noir'),
(17, 'Blanc'),
(18, 'Beige'),
(19, 'Gris'),
(20, 'Bleu'),
(21, 'Rouge'),
(22, 'Rose'),
(23, 'Vert'),
(24, 'Jaune'),
(25, 'Orange'),
(26, 'Violet'),
(27, 'Uni'),
(28, 'Fleuri'),
(29, 'Rayé'),
(30, 'À carreaux'),
(31, 'Pois'),
(32, 'Animalier'),
(33, 'Géométrique'),
(34, 'Femme'),
(35, 'Homme'),
(36, 'Enfant'),
(37, 'Bébé'),
(38, 'Fille'),
(39, 'Garçon'),
(40, 'Printemps'),
(41, 'Été'),
(42, 'Automne'),
(43, 'Hiver'),
(44, 'Mi-saison');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(180) NOT NULL,
  `siret` varchar(255) DEFAULT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `email`, `siret`, `roles`, `password`, `is_verified`) VALUES
(83, 'Emma', 'Martin', 'emma.martin@example.test', '73282932000001', '[\"ROLE_USER\"]', '$2y$13$nA5gT2tMJ0XUayTiAMAs2O6tAfhDHZmzbi6RbOk/JRk69.LOUVAg6', 1),
(84, 'Lucas', 'Bernard', 'lucas.bernard@example.test', '73282932000002', '[\"ROLE_USER\"]', '$2y$13$Xrf0XDBCIpYtm/ZUsFukYev1zlVzFLf1bcb2h6MtmLiM7rbb.2wIK', 1),
(85, 'Chloé', 'Dubois', 'chloe.dubois@example.test', '73282932000003', '[\"ROLE_USER\"]', '$2y$13$hc2j0QD8TeoHzHmDjUUet.m3CHbx8Kwif6ZrNjcFG9Ji4XkDf2Dh.', 1),
(86, 'Hugo', 'Thomas', 'hugo.thomas@example.test', '73282932000004', '[\"ROLE_USER\"]', '$2y$13$wwQNyB7pqw4tl3047getJeb9NKnfpqALzI5RpxEEvFK428mzSjMIO', 1),
(87, 'Camille', 'Robert', 'camille.robert@example.test', '73282932000005', '[\"ROLE_USER\"]', '$2y$13$uKI6MYsZQWfsFCHGamu/Q.ZFoOs1U5LZ2XtruQjAhyI3kLaBxJSGW', 1),
(88, 'Louis', 'Richard', 'louis.richard@example.test', '73282932000006', '[\"ROLE_USER\"]', '$2y$13$Q/8F30StUb3FtjTIGDlzB..cFUsVEQYcDcsXh8Q1gGTo.vsCQIFcC', 1),
(89, 'Manon', 'Petit', 'manon.petit@example.test', '73282932000007', '[\"ROLE_USER\"]', '$2y$13$tFkz.K3J1Z7ePdlJCM4gbuV9tdAb1g0nVFQ1wsfSJu8B3lXHukACK', 1),
(90, 'Nathan', 'Durand', 'nathan.durand@example.test', '73282932000008', '[\"ROLE_USER\"]', '$2y$13$Q6RCxOg5aJOxW6awKEBxWO3xRSY8/DdJemSlM1.e8AMDFqOQign5.', 1),
(91, 'Jade', 'Leroy', 'jade.leroy@example.test', '73282932000009', '[\"ROLE_USER\"]', '$2y$13$xcRF8m1IwzuOemhV8bRLG.NQEjoWPFZSZJwoX4YivLD56v3knIshy', 1),
(92, 'Gabriel', 'Moreau', 'gabriel.moreau@example.test', '73282932000010', '[\"ROLE_USER\"]', '$2y$13$0rBINEAdfc2U6..6Aab4wue9YKn0yXUr85DasYHbwfjqqErwjF60i', 1),
(93, 'Clara', 'Simon', 'clara.simon@example.test', '73282932000011', '[\"ROLE_SELLER\"]', '$2y$13$Ms7X553qMKeUJwnfoLAAOueKxISnLYqMxTnvhN7zk0aTWA4JJ9cQa', 1),
(94, 'Arthur', 'Laurent', 'arthur.laurent@example.test', '73282932000012', '[\"ROLE_SELLER\"]', '$2y$13$oVJjvr98PUvh9TAYlgxTSuYuocYE7fOKIEvqSvt.EWoXlmdiLwObu', 1),
(95, 'Alice', 'Lefebvre', 'alice.lefebvre@example.test', '73282932000013', '[\"ROLE_SELLER\"]', '$2y$13$L9LlIQSzSM9m69INRtumEuSkflcKJsWR3WeJeSTRRlf0bfa0XUkcS', 1),
(96, 'Thomas', 'Michel', 'thomas.michel@example.test', '73282932000014', '[\"ROLE_SELLER\"]', '$2y$13$PFDk6gTpBPCWQLhCSCOg0.RnFr.rC2tQj523ZslPP/DpHAHMEbPAe', 1),
(97, 'Élodie', 'Garcia', 'elodie.garcia@example.test', '73282932000015', '[\"ROLE_SELLER\"]', '$2y$13$OTUH.q4dZ1N0Z69yd.pxl.ntz2gY9Szbu3qffqtCoKVgHn3/S0MTS', 1),
(98, 'Maxime', 'David', 'maxime.david@example.test', '73282932000016', '[\"ROLE_SELLER\"]', '$2y$13$g9M2Gv3/aPnMbp8GTuPtK.jctPuQYaDCxJRvHLUPmwoPd3Ei4VsrG', 1),
(99, 'Sarah', 'Bertrand', 'sarah.bertrand@example.test', '73282932000017', '[\"ROLE_SELLER\"]', '$2y$13$aVpGcrpH3Gonm95ht9FjDOYezVR0r/XqfJD3IM88/i0CG4ZoBedHa', 1),
(100, 'Julien', 'Roux', 'julien.roux@example.test', '73282932000018', '[\"ROLE_SELLER\"]', '$2y$13$GeZKpA2F7tqBqUvK2Tq2uuW6Y7h1MgcuDVpyf2.rq.P8zDoOzLcEK', 1),
(101, 'Laura', 'Vincent', 'laura.vincent@example.test', '73282932000019', '[\"ROLE_SELLER\"]', '$2y$13$KrBc8NWRLwl9jN5xbYSgA.N/nkoVHMBpw/Ej2cie4kbTvRXJEQjf.', 1),
(102, 'Antoine', 'Fournier', 'antoine.fournier@example.test', '73282932000020', '[\"ROLE_SELLER\"]', '$2y$13$f6yuO/u6rPynP5KYWSNC8e3HbYBUmS.HuQyO9kpeGbjxJYuDwQ9wG', 1),
(103, 'Léa', 'Morel', 'lea.morel@example.test', '73282932000021', '[\"ROLE_SELLER\"]', '$2y$13$8iT3i4hbXKAf/lXlxZ2B0e6/psTdc9iLTkfjoYtnfG5g8pEGvfQpa', 1),
(104, 'Mathieu', 'Girard', 'mathieu.girard@example.test', '73282932000022', '[\"ROLE_SELLER\"]', '$2y$13$32yfabe3Qrv9tOKIE2Wd3OZmR.ys36Qq64B92KOUF.EhnVPtkEc8G', 1),
(105, 'Sophie', 'André', 'sophie.andre@example.test', '73282932000023', '[\"ROLE_SELLER\"]', '$2y$13$V2Tk0fiMxzxh2OwF8GeusO2y1L.YJihkmgqFdUgMseHUrXM0JM5T2', 1),
(106, 'Romain', 'Lefort', 'romain.lefort@example.test', '73282932000024', '[\"ROLE_SELLER\"]', '$2y$13$3XjYswADjq7fphedvVX5Vu.VKMIn3Stt7ImV/dfPO4qC0rs9JkISK', 1),
(107, 'Julie', 'Gautier', 'julie.gautier@example.test', '73282932000025', '[\"ROLE_SELLER\"]', '$2y$13$6SaNX5CAJYoEWYtr7NKlvejqMTVoIoWRflYIIF4MgfjfVN3k62MWm', 1),
(108, 'Nicolas', 'Bonnet', 'nicolas.bonnet@example.test', '73282932000026', '[\"ROLE_USER\"]', '$2y$13$xNQpCnR958BC3Mo14q613O6Oty.pPOcJfY7umiFLtJkPAXeboDLeW', 1),
(109, 'Anaïs', 'Dupont', 'anais.dupont@example.test', '73282932000027', '[\"ROLE_USER\"]', '$2y$13$k.93QG4R9sr49yU1Dif.neI/HFP1gHHrRYHQcMjwcE2KXO7aV5w4.', 1),
(110, 'Paul', 'Lambert', 'paul.lambert@example.test', '73282932000028', '[\"ROLE_USER\"]', '$2y$13$CbqxgSCjn0LJWa4Dcorbx.cY7q16125PuGlmPSSbKPotgX2chXVVW', 1),
(111, 'Margaux', 'Fontaine', 'margaux.fontaine@example.test', '73282932000029', '[\"ROLE_USER\"]', '$2y$13$CZVYnhIVccX6AlGHk5SmjeeCRtrK54LcdVM390RL57cq7ohnwCBGu', 1),
(112, 'Alexandre', 'Rousseau', 'alexandre.rousseau@example.test', '73282932000030', '[\"ROLE_USER\"]', '$2y$13$68yel5YXnTD7hDDvN.3IVO7MTjuiXliD4i2O.XNBVwgg6blIDzAA6', 1),
(113, 'Louise', 'Blanc', 'louise.blanc@example.test', '73282932000031', '[\"ROLE_USER\"]', '$2y$13$BFurNUN0K0gcQPRowhY4LecSzwpJPoaRmoD7gpVmyAHlGOYTlpcK2', 1),
(114, 'Baptiste', 'Guerin', 'baptiste.guerin@example.test', '73282932000032', '[\"ROLE_USER\"]', '$2y$13$HulXRLOLd9qH/sdW0fIyL.hg.fSOnvxEnBlErTOnpO82.hcgdjZ2S', 1),
(115, 'Océane', 'Muller', 'oceane.muller@example.test', '73282932000033', '[\"ROLE_USER\"]', '$2y$13$1Ivk5P2uAu13RAbTgj4TouIw1NO270Kq3BHamrXRGmxWudjydJEKq', 1),
(116, 'Valentin', 'Henry', 'valentin.henry@example.test', '73282932000034', '[\"ROLE_USER\"]', '$2y$13$tD9equfg..j9ZnkmadTuOOng4rtLJ43qmMiRWXBOMEkJVtD44cIlO', 1),
(117, 'Inès', 'Marchand', 'ines.marchand@example.test', '73282932000035', '[\"ROLE_USER\"]', '$2y$13$ztSKt.md6qZ4B9BwgvDrJO8UQilRwMFKMLHq/KpiDTgG7zJq1CfXu', 1),
(118, 'Thomas', 'Chevalier', 'thomas.chevalier@example.test', '73282932000036', '[\"ROLE_ADMIN\"]', '$2y$13$UCeqQk7/cYwBcXnCkWPdieN1ujbiuqIPnpGZW.ph4RF7IzSQNDUAG', 1),
(119, 'Marion', 'Robin', 'marion.robin@example.test', '73282932000037', '[\"ROLE_ADMIN\"]', '$2y$13$Lvd8lKh/Yp7yP95NPHflVey1VpZY2RHPo6kb4svnTSNRnbGYEoZkW', 1),
(120, 'Pierre', 'Masson', 'pierre.masson@example.test', '73282932000038', '[\"ROLE_ADMIN\"]', '$2y$13$38pq5B/VyQ3WdKr7XwYbq.9D1WM00nhOgfO0Mg8786t78D4eAkWtS', 1),
(121, 'Claire', 'Henry', 'claire.henry@example.test', '73282932000039', '[\"ROLE_ADMIN\"]', '$2y$13$BvFQChN4zjfhYMfuoryfs.0cwcr0kMmVSBF1XBJVp04eTXRhhIdxi', 1),
(122, 'Sébastien', 'Legrand', 'sebastien.legrand@example.test', '73282932000040', '[\"ROLE_ADMIN\"]', '$2y$13$8gbBnl93iGw5k59Ex2Crfek8cBDWh9sejqB.sesywvrVNuPUodPR.', 1),
(123, 'Julie', 'Perrin', 'julie.perrin@example.test', '73282932000041', '[\"ROLE_ADMIN\"]', '$2y$13$kswmQeb9PLH6iiAZgx3uB.VTfrl7fmfHjOtYOPoRz11yfFlRj9EQS', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adress`
--
ALTER TABLE `adress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5CECC7BEA76ED395` (`user_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `category_group`
--
ALTER TABLE `category_group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B3BA5A5A12469DE2` (`category_id`),
  ADD KEY `IDX_B3BA5A5AA76ED395` (`user_id`);

--
-- Index pour la table `products_tags`
--
ALTER TABLE `products_tags`
  ADD PRIMARY KEY (`products_id`,`tags_id`),
  ADD KEY `IDX_E3AB5A2C6C8A81A9` (`products_id`),
  ADD KEY `IDX_E3AB5A2C8D7B4FB4` (`tags_id`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `adress`
--
ALTER TABLE `adress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT pour la table `category_group`
--
ALTER TABLE `category_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adress`
--
ALTER TABLE `adress`
  ADD CONSTRAINT `FK_5CECC7BEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_B3BA5A5A12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_B3BA5A5AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `products_tags`
--
ALTER TABLE `products_tags`
  ADD CONSTRAINT `FK_E3AB5A2C6C8A81A9` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E3AB5A2C8D7B4FB4` FOREIGN KEY (`tags_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
