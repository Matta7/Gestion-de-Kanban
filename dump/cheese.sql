--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `cheese`
--

DROP TABLE IF EXISTS `cheese`;

CREATE TABLE `cheese` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `creator` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `cheese`
--

INSERT INTO `cheese` (`id`, `name`, `region`, `year`, `creator`, `image`) VALUES
(1, 'Camembert', 'Normandie', 1761, 'admin', '1.jpg'),
(2, 'Livarot', 'Normandie', 1850, 'admin', NULL),
(3, 'Brie', 'Seine et Marne', 999, 'admin', NULL),
(4, 'Emmetal', 'Suisse', 2021, 'admin', NULL),
(5, 'Saint-Nectaire', 'Saint-Nectaire', 2021, 'admin', NULL),
(6, 'Roquefort', 'Roquefort-Sur-Soulzon', 2022, 'admin', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cheese`
--
ALTER TABLE `cheese`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cheese`
--
ALTER TABLE `cheese`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;