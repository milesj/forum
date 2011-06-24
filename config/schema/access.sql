
DROP TABLE IF EXISTS `{prefix}access`;

CREATE TABLE `{prefix}access` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`access_level_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `access_level_id` (`access_level_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users with certain access' AUTO_INCREMENT=1;
