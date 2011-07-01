
CREATE TABLE IF NOT EXISTS `#__missingt_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(300) NOT NULL,
  `sha` varchar(40) NOT NULL,
  `note` varchar(250) NOT NULL,
  `text` longtext NOT NULL,
  `last_modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;
