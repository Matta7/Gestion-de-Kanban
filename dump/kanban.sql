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
  public int(1) default 1, -- 0 is public, anything else is private
  constraint pkkanban primary key (idKanban),
  constraint fkkanbancreatoraccount foreign key (creator) references accounts (login) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `kanban` (`nameKanban`, `descKanban`, `creator`, `public`) VALUES
('LW1', 'Projet Web à faire.', 'admin', 0),
('Courses', "Mes courses que j ai à faire + quelques trucs.", 'projet', -1),
('Kanban Privé', 'Kanban Privé', 'admin', -1);
COMMIT;