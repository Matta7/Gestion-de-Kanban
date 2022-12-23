--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `colonnes`
--

DROP TABLE IF EXISTS `colonnes`;

CREATE TABLE colonnes (
  idCol int(11) NOT NULL AUTO_INCREMENT,
  nameCol varchar(255) NOT NULL,
  orderCol int(2) NOT NULL, -- The order in which the columns will be displayed
  kanban int(11) NOT NULL,
  constraint pkcolonnes primary key (idCol),
  constraint fkcolonneskanbankanban foreign key (kanban) references kanban (idKanban) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `colonnes` (`nameCol`, `orderCol`, `kanban`) VALUES
('Stories', 0, 1),
('Terminées', 1, 1),
('Stories', 0, 2),
('Terminées', 1, 2),
('Stories', 0, 3),
('Terminées', 1, 3);
COMMIT;
