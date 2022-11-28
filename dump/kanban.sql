--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `Kanban`
--

DROP TABLE IF EXISTS `kanban`;

CREATE TABLE kanban (
  idKanban int(11) NOT NULL AUTO_INCREMENT,
  nameKanban varchar(255) NOT NULL,
  descKanban varchar(255) DEFAULT NULL,
  creator varchar(255) NOT NULL,
  constraint pkkanban primary key (idKanban),
  constraint fkkanbancreatoraccount foreign key (creator) references accounts (login) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

