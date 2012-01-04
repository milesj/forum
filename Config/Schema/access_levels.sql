
DROP TABLE IF EXISTS `{prefix}access_levels`;

CREATE TABLE `{prefix}access_levels` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(30) NOT NULL,
	`level` INT(11) NOT NULL,
	`isAdmin` TINYINT NOT NULL DEFAULT '0',
	`isSuper` TINYINT NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Access levels for users' AUTO_INCREMENT=1;

INSERT INTO `{prefix}access_levels` (`id`, `title`, `level`, `isAdmin`, `isSuper`) VALUES
	(1, 'Member', 1, 0, 0),
	(2, 'Moderator', 4, 0, 0),
	(3, 'Super Moderator', 7, 0, 1),
	(4, 'Administrator', 10, 1, 1);
