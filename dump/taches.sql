--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

DROP TABLE IF EXISTS `taches`;

CREATE TABLE taches (
  idTache int(11) NOT NULL,
  idCol int(11) NOT NULL,
  descTache varchar(255) NOT NULL,
  affectation varchar(255), -- The user who is in charge of this task
  dateLimite date,
  constraint pktaches primary key (idtache),
  constraint fktacheidcolColonnes foreign key (idcol) references colonnes (idCol) ON DELETE CASCADE,
  constraint fktacheaffectationAccounts foreign key (affectation) references accounts (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
