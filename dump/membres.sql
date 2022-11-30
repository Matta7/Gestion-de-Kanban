--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

DROP TABLE IF EXISTS `membres`;

CREATE TABLE membres (
  idKanban int(11) NOT NULL,
  login varchar(255) NOT NULL,
  constraint pkmembres primary key (idKanban,login),
  constraint fkmembresidKanbanKanban foreign key (idKanban) references kanban (idKanban) ON DELETE CASCADE,
  constraint fkmembresloginAccounts foreign key (login) references accounts (login) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
