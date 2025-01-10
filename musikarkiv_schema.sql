-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: sverker.se.mysql.service.one.com:3306
-- Tid vid skapande: 06 jan 2025 kl 09:09
-- Serverversion: 10.6.18-MariaDB-ubu2204
-- PHP-version: 8.1.2-1ubuntu2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `sverker_se`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_artists`
--

CREATE TABLE `wp_musikarkiv_artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primärnyckel för artister',
  `discogsid` bigint(20) DEFAULT NULL COMMENT 'Discogs-ID för artisten',
  `name` varchar(200) NOT NULL COMMENT 'Fullständigt namn på artisten',
  `alias` int(11) DEFAULT NULL COMMENT 'Om namnet är ett alias, peka på originalartisten',
  `biography` TEXT DEFAULT NULL COMMENT 'Biografi eller beskrivning av artisten',
  `localartist` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flagga: 1 för lokal artist, 0 för ej lokal',
  `official_site` varchar(200) DEFAULT NULL COMMENT 'Länk till artistens officiella webbplats',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Tidpunkt för skapandet',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Tidpunkt för senaste uppdateringen',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`alias`) REFERENCES `wp_musikarkiv_artists`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `unique_discogsid` (`discogsid`),
  INDEX `idx_name` (`name`),
  INDEX `idx_localartist` (`localartist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabell för att lagra information om artister';
;

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_gigartists`
--

CREATE TABLE `wp_musikarkiv_gigartists` (
  `added` datetime NOT NULL DEFAULT current_timestamp(),
  `gig` int(11) NOT NULL,
  `artist` int(11) NOT NULL,
  `alternateName` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_gigs`
--

CREATE TABLE `wp_musikarkiv_gigs` (
  `id` int(11) NOT NULL,
  `added` datetime NOT NULL DEFAULT current_timestamp(),
  `date` date NOT NULL,
  `venue` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `cancelled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_inventory`
--

CREATE TABLE `wp_musikarkiv_inventory` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primärnyckel för objekt i arkivet',
  `added` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Tidpunkt för när objektet lades till',
  `updated` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Tidpunkt för senaste uppdateringen',
  `sortedArtist` varchar(100) NOT NULL COMMENT 'Artistnamn för sortering',
  `title` varchar(100) NOT NULL COMMENT 'Titel eller beskrivning av objektet',
  `artist` varchar(100) NOT NULL COMMENT 'Namn på huvudartist',
  `type` int(11) DEFAULT NULL COMMENT 'Nyckel från inventory_typ',
  `discogsType` varchar(50) DEFAULT NULL COMMENT 'Item format från Discogs',
  `releaseYear` year DEFAULT NULL COMMENT 'Utgivningsår för objektet',
  `description` varchar(5000) DEFAULT NULL COMMENT 'Beskrivning av objektet',
  `donated` varchar(100) DEFAULT NULL COMMENT 'Information om donation',
  `link` varchar(200) DEFAULT NULL COMMENT 'Länk till mer information',
  `image` varchar(400) NOT NULL DEFAULT '' COMMENT 'Filväg till bild',
  `thumbnail` varchar(400) NOT NULL DEFAULT '' COMMENT 'Filväg till miniatyrbild',
  `collection` tinyint(1) NOT NULL COMMENT 'Flagga: Ingår i samling',
  `public` tinyint(1) NOT NULL COMMENT 'Flagga: Kan höras på Fyriskällan',
  `archived` tinyint(1) NOT NULL COMMENT 'Flagga: Arkiverad',
  `discogsID` bigint(20) DEFAULT NULL COMMENT 'Koppling till Discogs-ID',
  `discogsMaster` bigint(20) DEFAULT NULL COMMENT 'Koppling till Discogs master-ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabell för att lagra objekt i musikarkivet';

CREATE TABLE `wp_musikarkiv_artist_inventory` (
  `artist_id` int(11) NOT NULL COMMENT 'ID för artisten',
  `inventory_id` int(11) NOT NULL COMMENT 'ID för objektet i musikarkivet',
  PRIMARY KEY (`artist_id`, `inventory_id`),
  FOREIGN KEY (`artist_id`) REFERENCES `wp_musikarkiv_artists`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`inventory_id`) REFERENCES `wp_musikarkiv_inventory`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Relationstabell mellan artister och objekt i musikarkivet';

CREATE INDEX idx_artistID ON wp_musikarkiv_artist_inventory (artist_id);
CREATE INDEX idx_inventoryID ON wp_musikarkiv_artist_inventory (inventory_id);

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_search_stats`
--

CREATE TABLE `wp_musikarkiv_search_stats` (
  `date` date NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stats for search';

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_types`
--

CREATE TABLE `wp_musikarkiv_types` (
  `id` int(11) NOT NULL,
  `typ` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_persons`
--

CREATE TABLE wp_musikarkiv_persons (
    person_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Primärnyckel för personer',
    name VARCHAR(255) NOT NULL COMMENT 'Fullständigt namn på personen',
    description TEXT COMMENT 'Fritextbeskrivning om personen',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Tidpunkt för skapandet',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Tidpunkt för senaste uppdateringen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabell för att lagra information om personer';

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_wp_musikarkiv_band_person_relation`
--

CREATE TABLE wp_musikarkiv_band_person_relation (
    band_id INT NOT NULL COMMENT 'ID för bandet',
    person_id INT NOT NULL COMMENT 'ID för personen',
    role VARCHAR(255) COMMENT 'Beskrivning av personens roll i bandet',
    PRIMARY KEY (band_id, person_id),
    FOREIGN KEY (band_id) REFERENCES wp_musikarkiv_artists(id) ON DELETE CASCADE COMMENT 'Referens till bandets ID i artists-tabellen',
    FOREIGN KEY (person_id) REFERENCES wp_musikarkiv_persons(person_id) ON DELETE CASCADE COMMENT 'Referens till personens ID i persons-tabellen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Relationstabell mellan band och personer';

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_person_inventory_relation`
--

CREATE TABLE `wp_musikarkiv_person_inventory_relation` (
  `person_id` INT NOT NULL COMMENT 'ID för personen',
  `inventory_id` INT NOT NULL COMMENT 'ID för objektet i inventory',
  `role` VARCHAR(100) DEFAULT NULL COMMENT 'Beskrivning av personens roll (t.ex. kompositör, arrangör)',
  PRIMARY KEY (`person_id`, `inventory_id`),
  FOREIGN KEY (`person_id`) REFERENCES `wp_musikarkiv_artists`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`inventory_id`) REFERENCES `wp_musikarkiv_inventory`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Relation mellan personer och objekt i inventory';

-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_persons_gigs`
--

CREATE TABLE `wp_musikarkiv_persons_gigs` (
  `person_id` INT NOT NULL COMMENT 'ID för personen',
  `gig_id` INT NOT NULL COMMENT 'ID för eventet i gigs',
  PRIMARY KEY (`person_id`, `gig_id`),
  FOREIGN KEY (`person_id`) REFERENCES `wp_musikarkiv_persons`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`gig_id`) REFERENCES `wp_musikarkiv_gigs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Relation mellan personer och event i gigs';




-- --------------------------------------------------------

--
-- Tabellstruktur `wp_musikarkiv_venues`
--

CREATE TABLE `wp_musikarkiv_venues` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `wp_musikarkiv_artists`
--
ALTER TABLE `wp_musikarkiv_artists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index för tabell `wp_musikarkiv_gigartists`
--
ALTER TABLE `wp_musikarkiv_gigartists`
  ADD PRIMARY KEY (`gig`,`artist`);

--
-- Index för tabell `wp_musikarkiv_gigs`
--
ALTER TABLE `wp_musikarkiv_gigs`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `wp_musikarkiv_inventory`
--
ALTER TABLE `wp_musikarkiv_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `wp_musikarkiv_types`
--
ALTER TABLE `wp_musikarkiv_types`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `wp_musikarkiv_venues`
--
ALTER TABLE `wp_musikarkiv_venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `wp_musikarkiv_artists`
--
ALTER TABLE `wp_musikarkiv_artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wp_musikarkiv_gigs`
--
ALTER TABLE `wp_musikarkiv_gigs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wp_musikarkiv_inventory`
--
ALTER TABLE `wp_musikarkiv_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wp_musikarkiv_types`
--
ALTER TABLE `wp_musikarkiv_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `wp_musikarkiv_venues`
--
ALTER TABLE `wp_musikarkiv_venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
