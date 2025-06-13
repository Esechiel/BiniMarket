-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 11 juin 2025 à 12:06
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `binimarket`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon1` varchar(50) DEFAULT 'images/icons/petit/img-image.png',
  `icon2` text DEFAULT '\'images/icons/petit/img-image.png\'',
  `parent_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `icon1`, `icon2`, `parent_category_id`) VALUES
(1, 'Électronique', 'Ordinateurs, téléphones, accessoires, etc.', 'img-computer.png', 'img-computer1.png', NULL),
(2, 'Livres', 'Manuels universitaires, romans, BD, etc.', 'img-book.png', 'img-book1.png', NULL),
(3, 'Vêtements', 'Vêtements, chaussures, accessoires, etc.', 'img-t-shirt.png', 'img-t-shirt1.png', NULL),
(4, 'Nourriture', 'Plats préparés, ingrédients, snacks, etc.', 'img-restaurant.png', 'img-restaurant1.png', NULL),
(5, 'Services', 'Réparations, cours, aide aux devoirs, etc.', 'img-service.png', 'img-service1.png', NULL),
(6, 'Mobilier', 'Meubles, décoration, literie, etc.', 'img-mobilier.png', 'img-mobilier1.png', NULL),
(7, 'Transport', 'Vélos, trottinettes, covoiturage, etc.', 'img-bicycle.png', 'img-bicycle1.png', NULL),
(8, 'Divers', 'Tout ce qui ne rentre pas dans les autres catégories', 'img-others.png', 'img-others1.png', NULL),
(14, 'Ordinateurs', 'Regroupement de plusieurs ordinateurs de différents marques et performances', 'icon1_684417c6940f9.png', 'icon2_684417c6949cb.png', 1);

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `listing_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `last_activity` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `listing_id`, `creation_date`, `last_activity`) VALUES
(3, NULL, '2025-06-03 19:37:50', '2025-06-06 13:03:42'),
(4, NULL, '2025-06-03 20:02:04', '2025-06-08 13:05:41'),
(5, NULL, '2025-06-08 10:14:46', '2025-06-08 20:44:19'),
(6, NULL, '2025-06-08 17:37:33', '2025-06-08 21:23:19'),
(7, NULL, '2025-06-08 21:26:23', '2025-06-08 21:26:28'),
(8, 15, '2025-06-10 23:03:26', '2025-06-11 08:48:48'),
(9, 13, '2025-06-11 08:39:57', '2025-06-11 08:40:04'),
(10, 12, '2025-06-11 08:41:17', '2025-06-11 08:58:50');

-- --------------------------------------------------------

--
-- Structure de la table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `participant_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversation_participants`
--

INSERT INTO `conversation_participants` (`participant_id`, `conversation_id`, `user_id`, `joined_date`) VALUES
(15, 8, 11, '2025-06-10 23:03:26'),
(16, 8, 10, '2025-06-10 23:03:26'),
(17, 9, 10, '2025-06-11 08:39:57'),
(18, 9, 9, '2025-06-11 08:39:57'),
(19, 10, 10, '2025-06-11 08:41:17'),
(20, 10, 8, '2025-06-11 08:41:17');

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `listing_id`, `date_added`) VALUES
(19, 10, 12, '2025-06-11 08:42:17'),
(20, 11, 15, '2025-06-11 08:47:03'),
(21, 11, 18, '2025-06-11 08:55:48'),
(22, 8, 14, '2025-06-11 09:01:00');

-- --------------------------------------------------------

--
-- Structure de la table `listings`
--

CREATE TABLE `listings` (
  `listing_id` int(11) NOT NULL,
  `vendeur_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_negotiable` tinyint(1) DEFAULT 0,
  `type` enum('product','service') NOT NULL,
  `conditions` enum('Neuf','Presque neuf','Bon état','Etat moyen') DEFAULT NULL,
  `status` enum('active','vendu','inactive','rejeté','supprimé') DEFAULT 'active',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `views_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `listings`
--

INSERT INTO `listings` (`listing_id`, `vendeur_id`, `category_id`, `location_id`, `title`, `description`, `price`, `is_negotiable`, `type`, `conditions`, `status`, `creation_date`, `last_update_date`, `views_count`) VALUES
(10, 8, 3, 9, 'T-shirt', 'Bien beau...', 5000.00, 0, 'product', 'Neuf', 'inactive', '2025-06-08 23:29:39', '2025-06-11 10:17:11', 1),
(11, 8, 3, 9, 'T-shirt 6', 'bons', 5000.00, 1, 'product', 'Neuf', 'active', '2025-06-08 23:30:29', '2025-06-11 09:08:08', 4),
(12, 8, 3, 10, 'vetete ', 'Oa vxc n', 4000.00, 1, 'product', 'Presque neuf', 'active', '2025-06-08 23:31:43', '2025-06-11 08:40:56', 1),
(13, 9, 1, 6, 'HJVJD.F', 'HBKDKBJD', 6780.00, 1, 'product', 'Presque neuf', 'active', '2025-06-08 23:33:43', '2025-06-11 08:39:05', 2),
(14, 10, 3, 5, 'Vetements', 'Vêtements de luxe', 6000.00, 1, 'product', 'Neuf', 'active', '2025-06-10 22:59:28', '2025-06-11 09:07:58', 4),
(15, 10, 5, 11, 'Internet', 'Salut', 9000.00, 1, 'service', 'Neuf', 'active', '2025-06-10 23:01:02', '2025-06-11 08:43:19', 4),
(16, 6, 1, 7, 'vet', 'Salut', 2500.00, 1, 'product', 'Neuf', 'active', '2025-06-10 23:08:36', NULL, 0),
(17, 11, 4, 12, 'Auto', 'Bon blabla', 66789.00, 1, 'product', 'Neuf', 'active', '2025-06-11 08:53:23', '2025-06-11 09:07:54', 1),
(18, 11, 4, 5, 'pain', 'blabala...', 6789.00, 1, 'product', 'Presque neuf', 'active', '2025-06-11 08:54:27', '2025-06-11 10:11:20', 3),
(19, 11, 6, 5, 'Construction', 'Blabla....', 100000.00, 0, 'service', 'Bon état', 'active', '2025-06-11 08:55:29', '2025-06-11 08:55:32', 1),
(20, 9, 3, 7, 'pCAS', '68767890', 7000.00, 1, 'product', 'Neuf', 'active', '2025-06-11 09:10:45', '2025-06-11 09:45:32', 1),
(21, 8, 1, 8, 'TOTO', 'MERCI', 6000.00, 1, 'product', 'Etat moyen', 'active', '2025-06-11 10:16:58', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `listing_images`
--

CREATE TABLE `listing_images` (
  `image_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `listing_images`
--

INSERT INTO `listing_images` (`image_id`, `listing_id`, `image_path`, `is_primary`, `upload_date`) VALUES
(26, 10, 'img_68460ed3f29d8.jpg', 1, '2025-06-08 23:29:39'),
(27, 10, 'img_68460ed3f3fac.jpg', 0, '2025-06-08 23:29:40'),
(28, 10, 'img_68460ed400c11.jpg', 0, '2025-06-08 23:29:40'),
(29, 11, 'img_68460f05e1be2.jpg', 1, '2025-06-08 23:30:29'),
(30, 11, 'img_68460f05e3e58.jpg', 0, '2025-06-08 23:30:29'),
(31, 11, 'img_68460f05e4d27.jpg', 0, '2025-06-08 23:30:29'),
(32, 12, 'img_68460f4fda04d.jpg', 1, '2025-06-08 23:31:43'),
(33, 12, 'img_68460f4fdb4fc.jpg', 0, '2025-06-08 23:31:43'),
(34, 12, 'img_68460f4fdc26e.jpg', 0, '2025-06-08 23:31:43'),
(35, 13, 'img_68460fc7d82b1.jpg', 1, '2025-06-08 23:33:43'),
(36, 13, 'img_68460fc7d931b.jpg', 0, '2025-06-08 23:33:43'),
(37, 13, 'img_68460fc7da79a.jpg', 0, '2025-06-08 23:33:43'),
(38, 14, 'img_6848aac015174.jpg', 1, '2025-06-10 22:59:28'),
(39, 14, 'img_6848aac01630d.jpg', 0, '2025-06-10 22:59:28'),
(40, 14, 'img_6848aac017247.jpg', 0, '2025-06-10 22:59:28'),
(41, 15, 'img_6848ab1eaebde.jpg', 1, '2025-06-10 23:01:02'),
(42, 15, 'img_6848ab1eaf92a.jpg', 0, '2025-06-10 23:01:02'),
(43, 15, 'img_6848ab1eb0b9b.jpg', 0, '2025-06-10 23:01:02'),
(44, 16, 'img_6848ace487310.jpg', 1, '2025-06-10 23:08:36'),
(45, 16, 'img_6848ace4880f1.jpg', 0, '2025-06-10 23:08:36'),
(46, 16, 'img_6848ace488c39.jpg', 0, '2025-06-10 23:08:36'),
(47, 17, 'img_684935f3865fc.jpg', 1, '2025-06-11 08:53:23'),
(48, 17, 'img_684935f38786f.jpg', 0, '2025-06-11 08:53:23'),
(49, 17, 'img_684935f388bd5.jpg', 0, '2025-06-11 08:53:23'),
(50, 18, 'img_68493633d4421.jpg', 1, '2025-06-11 08:54:27'),
(51, 18, 'img_68493633d5400.jpg', 0, '2025-06-11 08:54:27'),
(52, 18, 'img_68493633d6d9b.jpg', 0, '2025-06-11 08:54:27'),
(53, 19, 'img_68493671b8d8e.JPEG', 1, '2025-06-11 08:55:29'),
(54, 19, 'img_68493671ba3d7.JPEG', 0, '2025-06-11 08:55:29'),
(55, 19, 'img_68493671bb32f.JPEG', 0, '2025-06-11 08:55:29'),
(56, 20, 'img_68493a050de1b.jpg', 1, '2025-06-11 09:10:45'),
(57, 20, 'img_68493a050fa89.jpg', 0, '2025-06-11 09:10:45'),
(58, 20, 'img_68493a05108b2.jpg', 0, '2025-06-11 09:10:45'),
(59, 21, 'img_6849498ae4c5a.jpg', 1, '2025-06-11 10:16:58');

-- --------------------------------------------------------

--
-- Structure de la table `listing_views`
--

CREATE TABLE `listing_views` (
  `view_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `viewer_id` int(11) DEFAULT NULL,
  `view_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `locations`
--

INSERT INTO `locations` (`location_id`, `name`, `description`) VALUES
(1, 'Bini Carefour Anta-Diop', NULL),
(2, 'Bini Guéritte', NULL),
(3, 'Bini Cité U', NULL),
(4, 'Bini Hotel Pakem', NULL),
(5, 'Bini Houro-Kessoum', NULL),
(6, 'Dang Borongo', NULL),
(7, 'Dang Gadabidou', NULL),
(8, 'Gada Dang', NULL),
(9, 'Marché Dang', NULL),
(10, 'Dang Total', NULL),
(11, 'Dang Tradex', NULL),
(12, 'Dang Sous-prefecture', NULL),
(13, 'Autre', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_send` varchar(20) DEFAULT NULL,
  `sent_date` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `content`, `image_send`, `sent_date`, `is_read`) VALUES
(47, 8, 11, 'Bonjour je suis interesse par le produit', NULL, '2025-06-10 23:03:59', 1),
(48, 8, 10, 'comment tu vas ? d\'accord tu as combien?', NULL, '2025-06-10 23:05:10', 1),
(49, 9, 10, 'bonjour', NULL, '2025-06-11 08:40:04', 0),
(50, 10, 10, 'ca va ?', NULL, '2025-06-11 08:41:24', 1),
(51, 8, 11, 'bien', NULL, '2025-06-11 08:48:48', 0),
(52, 10, 8, 'c comment ?', NULL, '2025-06-11 08:58:50', 0);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `creation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `content`, `related_id`, `is_read`, `creation_date`) VALUES
(25, 9, 'visite_profil', 'Sudo a consulté votre profil.', 1, 0, '2025-06-10 22:49:46'),
(26, 1, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 13, 0, '2025-06-10 22:51:08'),
(27, 9, 'visite_profil', 'Sudo a consulté votre profil.', 1, 0, '2025-06-10 22:51:24'),
(28, 10, 'visite_profil', 'Abba a consulté votre profil.', 11, 1, '2025-06-10 23:03:17'),
(29, 10, 'message', 'Vous avez reçu un message de Abba', 8, 1, '2025-06-10 23:03:59'),
(30, 11, 'message', 'Vous avez reçu un message de Nadia', 8, 1, '2025-06-10 23:05:10'),
(31, 11, 'visite_profil', 'Nadia a consulté votre profil.', 10, 1, '2025-06-10 23:05:19'),
(32, 10, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-10 23:05:59'),
(33, 10, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 14, 0, '2025-06-11 08:39:17'),
(34, 9, 'message', 'Vous avez reçu un message de Nadia', 9, 0, '2025-06-11 08:40:04'),
(35, 9, 'visite_profil', 'Nadia a consulté votre profil.', 10, 0, '2025-06-11 08:40:16'),
(36, 9, 'visite_profil', 'Nadia a consulté votre profil.', 10, 0, '2025-06-11 08:40:45'),
(37, 10, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 15, 0, '2025-06-11 08:41:07'),
(38, 8, 'message', 'Vous avez reçu un message de Nadia', 10, 0, '2025-06-11 08:41:24'),
(39, 8, 'visite_profil', 'Nadia a consulté votre profil.', 10, 0, '2025-06-11 08:41:33'),
(40, 8, 'visite_profil', 'Nadia a consulté votre profil.', 10, 0, '2025-06-11 08:42:00'),
(41, 8, 'favori', 'Votre annonce \"vetete \" a été ajoutée aux favoris.', 12, 0, '2025-06-11 08:42:17'),
(42, 8, 'visite_profil', 'Nadia a consulté votre profil.', 10, 1, '2025-06-11 08:42:30'),
(43, 11, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 16, 0, '2025-06-11 08:43:29'),
(44, 10, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 08:43:38'),
(45, 10, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 08:44:18'),
(46, 10, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 08:46:56'),
(47, 10, 'favori', 'Votre annonce \"Internet\" a été ajoutée aux favoris.', 15, 0, '2025-06-11 08:47:03'),
(48, 11, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 17, 0, '2025-06-11 08:47:20'),
(49, 8, 'visite_profil', 'Abba a consulté votre profil.', 11, 1, '2025-06-11 08:47:33'),
(50, 8, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 08:48:07'),
(51, 8, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 08:48:30'),
(52, 10, 'message', 'Vous avez reçu un message de Abba', 8, 0, '2025-06-11 08:48:48'),
(53, 11, 'favori', 'Votre annonce \"pain\" a été ajoutée aux favoris.', 18, 0, '2025-06-11 08:55:48'),
(54, 11, 'visite_profil', 'Test a consulté votre profil.', 8, 0, '2025-06-11 08:58:19'),
(55, 10, 'visite_profil', 'Test a consulté votre profil.', 8, 0, '2025-06-11 08:58:32'),
(56, 10, 'message', 'Vous avez reçu un message de Test', 10, 0, '2025-06-11 08:58:50'),
(57, 10, 'favori', 'Votre annonce \"Vetements\" a été ajoutée aux favoris.', 14, 0, '2025-06-11 09:01:00'),
(58, 11, 'transaction', 'Votre transaction pour l’annonce \"\" a été completé.', 18, 0, '2025-06-11 09:08:20'),
(59, 8, 'visite_profil', 'Abba a consulté votre profil.', 11, 0, '2025-06-11 09:08:27');

-- --------------------------------------------------------

--
-- Structure de la table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `user_id`, `method_name`, `is_default`) VALUES
(3, 6, 'Orange Money', 0),
(5, 1, 'Orange Money', 0),
(6, 10, 'Orange Money', 0),
(7, 11, 'Orange Money', 0);

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reported_user_id` int(11) DEFAULT NULL,
  `reported_listing_id` int(11) DEFAULT NULL,
  `reported_message_id` int(11) DEFAULT NULL,
  `reason` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('en cours','résolu','rejeté') DEFAULT 'en cours',
  `report_date` datetime DEFAULT current_timestamp(),
  `resolution_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewed_user_id` int(11) NOT NULL,
  `listing_id` int(11) DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`review_id`, `reviewer_id`, `reviewed_user_id`, `listing_id`, `rating`, `comment`, `review_date`) VALUES
(4, 10, 9, NULL, 4.0, 'Bon vendeur', '2025-06-11 08:40:38'),
(5, 10, 8, NULL, 1.0, 'tu m\'as arnaquer', '2025-06-11 08:41:54'),
(6, 11, 10, NULL, 3.0, 'tu as bien géré man', '2025-06-11 08:44:13'),
(7, 11, 8, NULL, 4.0, 'j\'ai rçu mon colis', '2025-06-11 08:48:07');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `acheteur_id` int(11) NOT NULL,
  `vendeur_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('en cours','completé','annulé','refusé') DEFAULT 'en cours',
  `phone` varchar(20) NOT NULL,
  `transaction_ref` varchar(40) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `listing_id`, `acheteur_id`, `vendeur_id`, `payment_method_id`, `amount`, `status`, `phone`, `transaction_ref`, `transaction_date`) VALUES
(13, 13, 1, 9, 5, 6780.00, 'completé', '656908743', 'TX_6848a8cc24e38', '2025-06-10 22:51:08'),
(14, 13, 10, 9, 6, 6780.00, 'completé', '654899938', 'TX_684932a513f4a', '2025-06-11 08:39:17'),
(15, 12, 10, 8, 6, 4000.00, 'completé', '666445678', 'TX_684933133c645', '2025-06-11 08:41:07'),
(16, 15, 11, 10, 7, 9000.00, 'completé', '654899936', 'TX_684933a1a6807', '2025-06-11 08:43:29'),
(17, 11, 11, 8, 7, 5000.00, 'completé', '654899937', 'TX_684934888857d', '2025-06-11 08:47:20'),
(18, 11, 11, 8, 7, 5000.00, 'completé', '654899939', 'TX_6849397404eb8', '2025-06-11 09:08:20');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `phone_momo` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'user-profile.png',
  `bio` text DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `grade` enum('user','admin','super_admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `phone_number`, `phone_momo`, `profile_picture`, `bio`, `location_id`, `registration_date`, `last_login`, `is_active`, `grade`) VALUES
(1, 'Sudo', 'superadmin@gmail.com', '$2y$10$Yw5xMaRsqoC9SqroxA12b.1jf9K59qfCQsPqdZVZdDi/YtMCoSdrC', '+237 612345678', NULL, 'user-profile.png', NULL, 1, '2025-05-30 10:31:19', '2025-06-11 09:07:19', 0, 'super_admin'),
(6, 'admin', 'admin@gmail.com', '$2y$10$1Z0w4Cg.mcvzv.GU9DP2HOuO1WOXiFu0M62eKrJq3VFZRfc4WmfPe', '+237 696969696', NULL, 'user-profile.png', NULL, 12, '2025-06-06 19:42:07', '2025-06-08 10:11:56', 0, 'admin'),
(8, 'Test', 'test@gmail.com', '$2y$10$GPoB8KQ7jREHzSTqZiLGJehF5IHOVVanfeJxXG3EjczmbLI3exz8C', '+237690000000', 2147483647, 'user-profile.png', NULL, 1, '2025-06-08 23:28:25', '2025-06-11 10:17:43', 0, 'user'),
(9, 'Bon', 'bon@gmail.com', '$2y$10$.AuP1DW1K7WmbYqDtTjz4uLQ/aVV41pe.6BZTD3ok58gtk97Wqijm', '+237690000000', 2147483647, 'user-profile.png', NULL, 11, '2025-06-08 23:32:46', '2025-06-11 09:11:02', 0, 'user'),
(10, 'Nadia', 'nadia@gmail.com', '$2y$10$c3zClibeprF/trZVjwdHnO.sKoNg4tDLg2FzKc8oi8jrizlYUXo8u', '696969696', NULL, 'user-profile.png', NULL, 12, '2025-06-10 22:57:12', '2025-06-11 08:43:01', 0, 'user'),
(11, 'Abba', 'abba@gmail.com', '$2y$10$TdlFzv/0RYSopm1KWLwT0.iL44MRI948TpJudBdyHuxYz8avDS3AK', '678098543', NULL, 'user-profile.png', NULL, 10, '2025-06-10 23:02:37', '2025-06-11 09:08:41', 0, 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Index pour la table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`participant_id`),
  ADD UNIQUE KEY `conversation_id` (`conversation_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`listing_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Index pour la table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`listing_id`),
  ADD KEY `idx_listings_vendeur` (`vendeur_id`),
  ADD KEY `idx_listings_category` (`category_id`),
  ADD KEY `idx_listings_location` (`location_id`),
  ADD KEY `idx_listings_status` (`status`),
  ADD KEY `idx_listings_type` (`type`);

--
-- Index pour la table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Index pour la table `listing_views`
--
ALTER TABLE `listing_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `viewer_id` (`viewer_id`);

--
-- Index pour la table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_messages_conversation` (`conversation_id`),
  ADD KEY `idx_messages_sender` (`sender_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`method_name`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `reported_user_id` (`reported_user_id`),
  ADD KEY `reported_listing_id` (`reported_listing_id`),
  ADD KEY `reported_message_id` (`reported_message_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `reviewer_id` (`reviewer_id`,`reviewed_user_id`,`listing_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `idx_reviews_reviewed` (`reviewed_user_id`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `idx_transactions_acheteur` (`acheteur_id`),
  ADD KEY `idx_transactions_vendeur` (`vendeur_id`),
  ADD KEY `transactions_ibfk_5` (`payment_method_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `listings`
--
ALTER TABLE `listings`
  MODIFY `listing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `listing_views`
--
ALTER TABLE `listing_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`vendeur_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `listing_views`
--
ALTER TABLE `listing_views`
  ADD CONSTRAINT `listing_views_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `listing_views_ibfk_2` FOREIGN KEY (`viewer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`reported_listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`reported_message_id`) REFERENCES `messages` (`message_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`acheteur_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`vendeur_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_5` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
