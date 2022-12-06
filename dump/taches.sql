--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

DROP TABLE IF EXISTS `taches`;

CREATE TABLE taches (
  idTache int(11) NOT NULL AUTO_INCREMENT,
  idCol int(11) NOT NULL,
  descTache varchar(255) NOT NULL,
  affectation varchar(255), -- The user who is in charge of this task
  dateLimite date,
  constraint pktaches primary key (idtache),
  constraint fktacheidcolColonnes foreign key (idcol) references colonnes (idCol) ON DELETE CASCADE,
  constraint fktacheaffectationAccounts foreign key (affectation) references accounts (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `taches` (`idCol`, `descTache`, `affectation`, `dateLimite`) VALUES
(1, 'faire lw', NULL, NULL),
(2, 'aller acheter des pates', NULL, NULL),
(2, 'prendre une douche', NULL, NULL);
COMMIT;