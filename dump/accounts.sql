SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `accounts`
--
DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `name` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'user',
  constraint pkaccounts primary key (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `accounts`
--

INSERT INTO `accounts` (`name`, `login`, `password`, `status`) VALUES
('Admin', 'admin', '$2y$10$XtNp3LEzMBHbloxGUqT/weWppoWPicU/jg73R3TgmQyhjIZVbCtGK', 'admin'),
('Vallée', 'vallee', '$2y$10$XtNp3LEzMBHbloxGUqT/weWppoWPicU/jg73R3TgmQyhjIZVbCtGK', 'user');
COMMIT;
